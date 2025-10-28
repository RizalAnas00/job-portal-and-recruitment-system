<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
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
        $query = JobPosting::with('company', 'skills')->latest();

        // Jika yang login adalah 'company', tampilkan hanya lowongan milik mereka.
        if ($user->hasRole('company') && $user->company) {
            if (!$request->boolean('all')) {
                $query->where('id_company', $user->company->id);
            }
        }

        $jobPostings = $query->paginate(10);
        return view('job_postings.index', compact('jobPostings'));
    }

    public function show(JobPosting $jobPosting)
    {
        // Eager load relasi untuk ditampilkan di halaman detail
        $jobPosting->load('company', 'skills');
        return view('job_postings.show', compact('jobPosting'));
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
        $currentPostCount = $company->jobPostings()->where('status', 'open')->count();
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
            'salary_range' => 'nullable|string|max:255',
            'closing_date' => 'nullable|date',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        try {
            // --- MULAI TRANSAKSI DI SINI ---
            DB::transaction(function () use ($company, $validatedData, $request) {
                // 1. Buat lowongan
                $jobPosting = $company->jobPostings()->create([
                    'job_title' => $validatedData['job_title'],
                    'location' => $validatedData['location'],
                    'job_type' => $validatedData['job_type'],
                    'job_description' => $validatedData['job_description'],
                    'salary_range' => $validatedData['salary_range'],
                    'closing_date' => $validatedData['closing_date'],
                    'status' => 'open',
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

        $skills = Skill::orderBy('skill_name')->get();
        return view('job_postings.edit', compact('jobPosting', 'skills'));
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

        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'location' => 'required|string|max:255',
            'job_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'internship', 'temporary', 'freelance', 'remote'])],
            'salary_range' => 'nullable|string|max:100',
            'posted_date' => 'nullable|date',
            'closing_date' => 'nullable|date|after_or_equal:posted_date',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id'
        ]);

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
