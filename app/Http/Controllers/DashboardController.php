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
        }

        // Add other role-based dashboards here as needed

        // Default dashboard view
        return view('dashboard');
    }

    // Private function to redirect according to user role
    private function companyDashboard(User $user) {
        if ($user->hasRole('company')) {
            $company = $user->company;
            $jobPostingsCount = $company->jobPostings()->count();
            $totalApplicantsCount = $company->totalApplicants();
            $activeJobPostingsCount = $company->jobPostings()->where('status', 'active')->count();
            $hiredCandidatesCount = $company->hiredCandidates();

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
