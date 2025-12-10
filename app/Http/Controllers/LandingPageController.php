<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LandingPageController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $latestJobs = JobPosting::latest()->take(3)->get();
        $companyCount = Company::count();
        $companies = Company::inRandomOrder()->take(10)->get();
        // Log::info('Landing page accessed by guest user.', ['latest_jobs_count' => $latestJobs->count()]);

        return view('welcome', compact('latestJobs', 'companyCount', 'companies'));
    }
}