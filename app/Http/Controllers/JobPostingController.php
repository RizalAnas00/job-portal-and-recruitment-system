<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        JobPosting::refreshScheduledStatuses();
        // $query = JobPosting::with('company', 'skills')->latest();
        
        $query = JobPosting::with(['company', 'skills']);
        if($user->hasRole('user')) {
            $query->where('posted_date', '<=', now())
                ->where(function($q) {
                    $q->whereNull('closing_date')
                        ->orWhere('closing_date', '>=', now());
                })
                ->whereNotIn('status', ['draft', 'archived']);
        }

        if ($user->hasRole('company')) {
            if ($user->company && !$request->boolean('all')) {
                $query->where('id_company', $user->company->id);
            }
        }

        $query->when($request->filled('search'), function($q) use ($request) {
            $search = $request->search;

            $q->where(function($x) use ($search) {
                $x->where('job_title', 'LIKE', "%$search%")
                ->orWhere('location', 'LIKE', "%$search%")
                ->orWhereHas('company', fn($c) => $c->where('company_name', 'LIKE', "%$search%"))
                ->orWhereHas('skills', fn($s) => $s->where('skill_name', 'LIKE', "%$search%"));
            });

            $q->select('*')->selectSub(function($sq) use ($search) {
                $sq->selectRaw("
                    CASE
                        WHEN job_title = ? THEN 6
                        WHEN job_title LIKE ? THEN 5
                        WHEN job_title LIKE ? THEN 4
                        WHEN EXISTS (SELECT 1 FROM companies c WHERE c.id = job_postings.id_company AND c.company_name LIKE ?) THEN 3
                        WHEN location LIKE ? THEN 2
                        WHEN EXISTS (
                            SELECT 1 FROM job_posting_skill jps
                            JOIN skills s ON s.id = jps.id_skill
                            WHERE jps.id_job_posting = job_postings.id
                            AND s.skill_name LIKE ?
                        ) THEN 1
                        ELSE 0
                    END
                ", [
                    $search,          // exact
                    "$search%",       // starts with search
                    "%$search%",      // contains anywhere
                    "%$search%",      // company
                    "%$search%",      // location
                    "%$search%"       // skill
                ]);
            }, 'relevance');

            // Sorting berdasarkan relevansi lalu terbaru
            $q->orderByDesc('relevance')
            ->orderBy('created_at', 'desc');
        });

        // Jika yang login adalah 'company', tampilkan hanya lowongan milik mereka.
        if ($user->hasRole('company')) {

            if ($user->company) {
                // Log::info("company : ". $user->company);
                if (!$request->boolean('all')) {
                    $query->where('id_company', $user->company->id);
                }
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($user->hasRole('user')) {
                $query->where('status', $request->status)
                    ->whereNotIn('status', ['draft', 'archived']);
            } else {
                $query->where('status', $request->status);
            }
        }
        
        $jobSeeker = $user->jobSeeker;
        $appliedJobIds = $jobSeeker
        ? $jobSeeker->applications()->pluck('id_job_posting')->toArray()
            : [];
            
        $jobPostings = $query->paginate(12);
        // Log::info("query : ", $jobPostings->toArray());

        foreach ($jobPostings as $job) {
            $job->hasApplied = in_array($job->id, $appliedJobIds);
        }

        return view('job_postings.index', compact('jobPostings'));
    }

    public function show(JobPosting $jobPosting)
    {
        JobPosting::refreshScheduledStatuses();
        
        // Eager load relasi yang dibutuhkan accessors/methods
        $jobPosting->load('company', 'skills');

        /** @var \App\Models\User|null */
        $user = Auth::user();

        $matchedSkills  = $jobPosting->getMatchedSkillsWith($user);
        $matchCount     = count($matchedSkills);
        
        $isExpired      = $jobPosting->is_expired;
        $hoursLeft      = $jobPosting->hours_left;
        $isUrgent       = $jobPosting->is_urgent;

        $isCompanyOwner = $jobPosting->isOwnedBy($user);
        $hasApplied     = $jobPosting->hasApplicant($user);

        return view('job_postings.show', compact(
            'jobPosting',
            'matchedSkills',
            'matchCount',
            'isExpired',
            'isUrgent',
            'hoursLeft',
            'isCompanyOwner',
            'hasApplied'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = Auth::user()->company;

        // Pastikan perusahaan punya langganan aktif
        if (!$company || !$company->activeSubscription) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki langganan aktif untuk memposting lowongan.');
        }

        // Hitung jumlah lowongan yang sedang 'open'
        $currentPostCount = $company->jobPostings()->where('status', '!=','draft')->count();
        // Ambil batas dari paket langganan
        $postLimit = $company->activeSubscription->plan->job_post_limit;

        // Cek apakah batas sudah tercapai
        if ($currentPostCount >= $postLimit) {
            return redirect()->route('dashboard') // atau ke halaman kelola lowongan
                ->with('error', "Anda telah mencapai batas maksimal ({$postLimit}) lowongan pekerjaan untuk paket Anda.");
        }

        $skills = Cache::remember('skills_list',600 ,function () {
            return Skill::orderBy('skill_name')->get();
        });
        
        return view('job_postings.create', compact('skills'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->company;

        // Lakukan pengecekan sekali lagi sebelum menyimpan
        if (!$company || !$company->activeSubscription) {
            return back()->with('error', 'Anda tidak memiliki langganan aktif.');
        }
        $currentPostCount = $company->jobPostings()->where('status', 'open')->count();
        $postLimit = $company->activeSubscription->plan->job_post_limit;
        if ($currentPostCount >= $postLimit) {
            return back()->with('error', "Gagal menyimpan. Anda telah mencapai batas maksimal ({$postLimit}) lowongan pekerjaan.");
        }

        $validatedData = $request->validate([
            'job_title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:full_time,part_time,contract,internship,temporary,freelance,remote',
            'job_description' => 'required|string',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'posted_date' => 'required|date',
            'closing_date' => 'required|date|after:posted_date',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        try {
            // --- MULAI TRANSAKSI DI SINI ---
            DB::transaction(function () use ($company, $validatedData, $request) {
                $openAt = Carbon::parse($validatedData['posted_date']);
                $closeAt = Carbon::parse($validatedData['closing_date']);
                $status = JobPosting::statusForSchedule($openAt, $closeAt);

                // 1. Buat lowongan
                $jobPosting = $company->jobPostings()->create([
                    'job_title' => $validatedData['job_title'],
                    'location' => $validatedData['location'],
                    'job_type' => $validatedData['job_type'],
                    'job_description' => $validatedData['job_description'],
                    'min_salary' => $validatedData['min_salary'] ?? null,
                    'max_salary' => $validatedData['max_salary'] ?? null,
                    'posted_date' => $openAt,
                    'closing_date' => $closeAt,
                    'status' => $status,
                ]);

                // 2. Lampirkan skills jika ada
                if ($request->has('skills')) {
                    $jobPosting->skills()->attach($validatedData['skills']);
                }
            });
            // --- AKHIR TRANSAKSI ---

            return redirect()->route('job-postings.index')->with('success', 'Lowongan pekerjaan berhasil dipublikasikan.');

        } catch (\Exception $e) {
            // Jika terjadi error di dalam transaksi, kembalikan dengan pesan error.
            // Tidak ada data yang akan tersimpan di database.
            Log::error('Job posting creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan lowongan. Tidak ada data yang disimpan.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobPosting $jobPosting)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        
        if (!$user->hasRole('admin') && !($user->hasRole('company') && $user->company?->id === $jobPosting->id_company)) {
            abort(403, 'AKSES DITOLAK');
        }

        if($jobPosting->hasApplicants()) {
            return redirect()->route('job-postings.index')
                ->with('error', 'Maaf, lowongan dengan pelamar aktif(tidak berstatus accepted ataupun rejected) tidak dapat diedit.');
        }

        $skills = Skill::orderBy('skill_name')->get();
        $selectedSkillIds = $jobPosting->skills()->pluck('skills.id');

        return view('job_postings.edit', compact('jobPosting', 'skills', 'selectedSkillIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobPosting $jobPosting)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Otorisasi: Hanya admin atau pemilik perusahaan yang bisa mengupdate
        if (!$user->hasRole('admin') && !($user->hasRole('company') && $user->company?->id === $jobPosting->id_company)) {
            abort(403, 'AKSES DITOLAK');
        }

        if($jobPosting->hasApplicants()) {
            return redirect()->route('job-postings.index')
                ->with('error', 'Maaf, lowongan dengan pelamar aktif(tidak berstatus accepted ataupun rejected) tidak dapat diedit.');
        }

        Log::info("request : ", $request->all());

        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'location' => 'required|string|max:255',
            'job_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'internship', 'temporary', 'freelance', 'remote'])],
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',           
            'posted_date' => 'required|date',
            'closing_date' => 'required|date|after:posted_date',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id'
        ]);

        Log::info("validated : ", $validated);

        $openAt = Carbon::parse($validated['posted_date']);
        $closeAt = Carbon::parse($validated['closing_date']);
        $validated['status'] = JobPosting::statusForSchedule($openAt, $closeAt);

        $jobPosting->update($validated);

        // 'sync' akan memperbarui relasi: menghapus yang tidak dipilih dan menambah yang baru.
        if (!empty($validated['skills'])) {
            $jobPosting->skills()->sync($validated['skills']);
        } else {
            $jobPosting->skills()->detach();
        }

        return redirect()->route('job-postings.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    /**
     * Update the status of the specified job posting.
     */
    public function updateStatus(Request $request, JobPosting $jobPosting)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Otorisasi: Hanya admin atau pemilik perusahaan yang bisa mengupdate status
        if (!$user->hasRole('admin') && !($user->hasRole('company') && $user->company?->id === $jobPosting->id_company)) {
            abort(403, 'AKSES DITOLAK');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'open', 'paused', 'closed', 'archived'])],
        ]);

        $jobPosting->update(['status' => $validated['status']]);

        return back()->with('success', 'Status lowongan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPosting $jobPosting)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Otorisasi: Hanya admin atau pemilik perusahaan yang bisa menghapus
        if (!$user->hasRole('admin') && !($user->hasRole('company') && $user->company?->id === $jobPosting->id_company)) {
            abort(403, 'AKSES DITOLAK');
        }

        $jobPosting->delete();
        return redirect()->route('job-postings.index')->with('success', 'Lowongan berhasil dihapus.');
    }
}
