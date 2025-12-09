<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard() {
        $user = Auth::user();

        if ($user->role->name === 'company') {
            return $this->companyDashboard($user);
        } elseif ($user->role->name === 'admin') {
            //
        } elseif ($user->role->name === 'user') {
            //
        }

        // Add other role-based dashboards here as needed

        // Default dashboard view
        return view('dashboard');
    }

    private function companyDashboard(User $user)
    {
        if ($user->hasRole('company')) {
            $company = $user->company;
            
            // Cek profil perusahaan
            if (!$company) {
                return redirect()->route('company.profile.create')
                    ->with('info', 'Silakan lengkapi profil perusahaan Anda terlebih dahulu.');
            }
            
            // 1. Statistik Card Data
            $jobPostingsCount = $company->jobPostings()->count();
            $totalApplicantsCount = $company->totalApplicants() ?? 0;
            $activeJobPostingsCount = $company->jobPostings()->where('status', 'open')->count();
            $hiredCandidatesCount = $company->hiredCandidates() ?? 0;

            // 2. Data Tabel: 5 Pelamar Terakhir (Nama & Posisi yang dilamar)
            $jobSeekerApplyAt = DB::table('applications')
                ->join('job_seekers', 'applications.id_job_seeker', '=', 'job_seekers.id')
                ->join('job_postings', 'applications.id_job_posting', '=', 'job_postings.id') // Join ke posting agar tahu melamar kerja apa
                ->where('job_postings.id_company', $company->id)
                ->select(
                    'job_seekers.first_name as first_name', 'job_seekers.last_name as last_name', 
                    'job_postings.job_title as position_name', 
                    'applications.created_at'
                )
                ->orderBy('applications.created_at', 'desc')
                ->limit(5)
                ->get();

            // 3. Data Chart: Statistik Pendaftar 30 Hari Terakhir
            $chartData = DB::table('applications')
                ->join('job_postings', 'applications.id_job_posting', '=', 'job_postings.id')
                ->where('job_postings.status', 'open')
                ->where('job_postings.id_company', $company->id)
                ->where('applications.application_date', '>=', Carbon::now()->subDays(30))
                ->selectRaw('DATE(applications.application_date) as date, COUNT(*) as aggregate')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            return view('dashboard', compact(
                'company',
                'jobPostingsCount',
                'totalApplicantsCount',
                'activeJobPostingsCount',
                'hiredCandidatesCount',
                'jobSeekerApplyAt',
                'chartData' 
            ));
        }
    }
}
