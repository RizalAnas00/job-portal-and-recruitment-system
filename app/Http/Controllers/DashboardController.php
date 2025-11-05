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

            if (!$company) {
                return view('dashboard', [
                    'company' => null,
                    'jobPostingsCount' => 0,
                    'totalApplicantsCount' => 0,
                    'activeJobPostingsCount' => 0,
                    'hiredCandidatesCount' => 0,
                ]);
            }

            $jobPostingsCount = $company->jobPostings()->count();
            $totalApplicantsCount = $company->totalApplicants() ?? 0;
            $activeJobPostingsCount = $company->jobPostings()->where('status', 'active')->count();
            $hiredCandidatesCount = $company->hiredCandidates() ?? 0;

            return view('dashboard', compact(
                'company',
                'jobPostingsCount',
                'totalApplicantsCount',
                'activeJobPostingsCount',
                'hiredCandidatesCount'
            ));
        }
    }

}
