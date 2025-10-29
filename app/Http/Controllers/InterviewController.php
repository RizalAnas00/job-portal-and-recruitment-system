<?php

namespace App\Http\Controllers;

use App\Actions\SendJobSeekerNotification;
use App\Models\Application;
use App\Models\Interview;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct(
        private readonly SendJobSeekerNotification $sendJobSeekerNotification
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Interview::with('application.jobPosting', 'application.jobSeeker.user')->latest();

        if ($user->hasRole('company')) {
            // Company hanya bisa melihat interview untuk lowongan mereka
            $companyId = $user->company->id;
            $query->whereHas('application.jobPosting', function ($q) use ($companyId) {
                $q->where('id_company', $companyId);
            });
        } elseif ($user->hasRole('user')) {
            // User (job seeker) hanya bisa melihat interview miliknya
            $jobSeekerId = $user->jobSeeker->id;
            $query->whereHas('application', function ($q) use ($jobSeekerId) {
                $q->where('job_seeker_id', $jobSeekerId);
            });
        }
        // Admin bisa melihat semua, jadi tidak perlu filter tambahan.

        $interviews = $query->paginate(10);

        return view('interviews.index', compact('interviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Application $application)
    {
        if (Auth::user()->company?->id !== $application->jobPosting->id_company) {
            abort(403, 'AKSES DITOLAK');
        }
        return view('interviews.create', compact('application'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_application' => 'required|exists:applications,id',
            'interviewer_name' => 'required|string|max:255',
            'interview_date' => 'required|date|after:now',
            'interview_type' => ['required', Rule::in(['online', 'offline', 'phone_screen'])],
            'location' => 'required|string|max:255', // Bisa berupa link meeting atau alamat fisik
            'notes' => 'nullable|string',
        ]);

        $application = Application::findOrFail($validated['id_application']);
        // Otorisasi: Pastikan company adalah pemilik lamaran ini
        if (Auth::user()->company?->id !== $application->jobPosting->id_company) {
            abort(403, 'AKSES DITOLAK');
        }

        $interview = Interview::create($validated);

        // Update status lamaran menjadi 'interviewing'
        $application->update(['status' => 'interviewing']);

        $message = $this->composeInterviewMessage(
            $application,
            $validated['interview_date'],
            $validated['interview_type'],
            'dijadwalkan',
            "Status lamaran Anda kini berada pada tahap interview."
        );

        $this->notifyJobSeeker($application, $message);

        return redirect()->route('interviews.index')->with('success', 'Jadwal wawancara berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Interview $interview)
    {
        $user = Auth::user();

        // Otorisasi
        $canView = false;
        if ($user->hasRole('admin')) {
            $canView = true;
        } elseif ($user->hasRole('company') && $user->company?->id === $interview->application->jobPosting->id_company) {
            $canView = true;
        } elseif ($user->hasRole('user') && $user->jobSeeker?->id === $interview->application->job_seeker_id) {
            $canView = true;
        }

        if (!$canView) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('interviews.show', compact('interview'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Interview $interview)
    {
        $user = Auth::user();
        // Otorisasi: Hanya admin atau company pemilik yang bisa mengedit
        if (!$user->hasRole('admin') && $user->company?->id !== $interview->application->jobPosting->id_company) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('interviews.edit', compact('interview'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Interview $interview)
    {
        $user = Auth::user();
        // Otorisasi: Hanya admin atau company pemilik yang bisa memperbarui
        if (!$user->hasRole('admin') && $user->company?->id !== $interview->application->jobPosting->id_company) {
            abort(403, 'AKSES DITOLAK');
        }

        $validated = $request->validate([
            'interviewer_name' => 'required|string|max:255',
            'interview_date' => 'required|date|after:now',
            'interview_type' => ['required', Rule::in(['online', 'offline', 'phone_screen'])],
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $interview->update($validated);

        $application = $interview->application;

        $message = $this->composeInterviewMessage(
            $application,
            $validated['interview_date'],
            $validated['interview_type'],
            'diperbarui',
            'Silakan cek detail terbaru pada halaman interview.'
        );

        $this->notifyJobSeeker($application, $message);

        return redirect()->route('interviews.index')->with('success', 'Jadwal wawancara berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Interview $interview)
    {
        $user = Auth::user();
        // Otorisasi: Hanya admin atau company pemilik yang bisa menghapus
        if (!$user->hasRole('admin') && $user->company?->id !== $interview->application->jobPosting->id_company) {
            abort(403, 'AKSES DITOLAK');
        }

        $application = $interview->application;

        $interview->delete();

        if ($application) {
            $application->update(['status' => 'under_review']);

            $message = $this->composeInterviewMessage(
                $application,
                $interview->interview_date,
                $interview->interview_type,
                'dibatalkan',
                'Tim perusahaan akan menghubungi Anda jika ada jadwal pengganti.'
            );

            $this->notifyJobSeeker($application, $message);
        }

        return redirect()->route('interviews.index')->with('success', 'Jadwal wawancara berhasil dibatalkan.');
    }

    private function notifyJobSeeker(?Application $application, string $message): void
    {
        if (!$application) {
            return;
        }

        $application->loadMissing('jobSeeker', 'jobPosting.company');

        if (!$application->jobSeeker) {
            return;
        }

        ($this->sendJobSeekerNotification)(
            $application->jobSeeker,
            $application->jobPosting?->company,
            $message,
            route('interviews.index')
        );
    }

    private function composeInterviewMessage(
        ?Application $application,
        $interviewDate,
        ?string $interviewType,
        string $verb,
        string $tailMessage
    ): string {
        $jobTitle = $application?->jobPosting?->job_title ?? 'posisi terkait';
        $companyName = $application?->jobPosting?->company?->company_name ?? 'perusahaan';

        $carbonDate = null;

        if ($interviewDate) {
            $carbonDate = $interviewDate instanceof Carbon
                ? $interviewDate
                : Carbon::parse($interviewDate);
        }

        $formattedDate = $carbonDate?->translatedFormat('d M Y H:i') ?? '-';
        $typeLabel = $interviewType
            ? Str::of($interviewType)->replace('_', ' ')->title()
            : 'Interview';

        return "Jadwal interview untuk {$jobTitle} di {$companyName} telah {$verb} pada {$formattedDate} ({$typeLabel}). {$tailMessage}";
    }
}
