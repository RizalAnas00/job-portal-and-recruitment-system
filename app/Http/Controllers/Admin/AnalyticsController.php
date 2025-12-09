<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\JobPosting;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // User Statistics
        $userStats = [
            'total' => User::whereHas('role', fn($q) => $q->whereIn('name', ['user', 'company']))->count(),
            'active' => User::where('is_active', true)
                ->whereHas('role', fn($q) => $q->whereIn('name', ['user', 'company']))->count(),
            'job_seekers' => User::whereHas('role', fn($q) => $q->where('name', 'user'))->count(),
            'companies' => User::whereHas('role', fn($q) => $q->where('name', 'company'))->count(),
            'new_this_period' => User::whereHas('role', fn($q) => $q->whereIn('name', ['user', 'company']))
                ->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Job Posting Statistics
        // Note: JobPosting doesn't have timestamps, so we use posted_date for new_this_period
        $jobStats = [
            'total' => JobPosting::count(),
            'active' => JobPosting::where('status', 'open')
                ->where('moderation_status', 'approved')->count(),
            'pending' => JobPosting::where('moderation_status', 'pending')->count(),
            'closed' => JobPosting::where('status', 'closed')->count(),
            'new_this_period' => JobPosting::whereNotNull('posted_date')
                ->whereBetween('posted_date', [$startDate, $endDate])->count(),
        ];

        // Application Statistics
        $applicationStats = [
            'total' => Application::count(),
            'applied' => Application::where('status', 'applied')->count(),
            'interviewing' => Application::where('status', 'interviewing')->count(),
            'hired' => Application::where('status', 'hired')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
            'new_this_period' => Application::whereBetween('application_date', [$startDate, $endDate])->count(),
        ];

        // Conversion Rates
        $conversionRates = [
            'apply_to_interview' => $applicationStats['total'] > 0 
                ? round(($applicationStats['interviewing'] / $applicationStats['total']) * 100, 2) 
                : 0,
            'interview_to_hire' => $applicationStats['interviewing'] > 0 
                ? round(($applicationStats['hired'] / $applicationStats['interviewing']) * 100, 2) 
                : 0,
            'apply_to_hire' => $applicationStats['total'] > 0 
                ? round(($applicationStats['hired'] / $applicationStats['total']) * 100, 2) 
                : 0,
        ];

        // Financial Statistics
        $financialStats = [
            'total_revenue' => PaymentTransaction::where('status', 'success')
                ->sum('amount'),
            'revenue_this_period' => PaymentTransaction::where('status', 'success')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'pending_payments' => PaymentTransaction::where('status', 'pending')->count(),
            'active_subscriptions' => CompanySubscription::where('status', 'active')
                ->where('end_date', '>', now())->count(),
        ];

        // Recent Activity
        // Note: JobPosting doesn't have timestamps, so we order by posted_date or id
        $recentJobs = JobPosting::with('company')
            ->orderByRaw('COALESCE(posted_date, id) DESC')
            ->limit(5)
            ->get();
        $recentApplications = Application::with(['jobSeeker.user', 'jobPosting.company'])
            ->whereNotNull('application_date')
            ->orderBy('application_date', 'DESC')
            ->limit(5)
            ->get();

        return view('admin.analytics.index', compact(
            'userStats',
            'jobStats',
            'applicationStats',
            'conversionRates',
            'financialStats',
            'recentJobs',
            'recentApplications',
            'period'
        ));
    }

    /**
     * Export analytics data to CSV.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'users'); // users, jobs, applications, payments
        
        $filename = 'analytics_' . $type . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            
            switch ($type) {
                case 'users':
                    fputcsv($file, ['ID', 'Email', 'Role', 'Status', 'Created At']);
                    User::whereHas('role', fn($q) => $q->whereIn('name', ['user', 'company']))
                        ->with('role')
                        ->chunk(100, function($users) use ($file) {
                            foreach ($users as $user) {
                                fputcsv($file, [
                                    $user->id,
                                    $user->email,
                                    $user->role->name ?? 'N/A',
                                    $user->is_active ? 'Active' : 'Inactive',
                                    $user->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }
                        });
                    break;
                    
                case 'jobs':
                    fputcsv($file, ['ID', 'Title', 'Company', 'Status', 'Moderation Status', 'Posted Date']);
                    JobPosting::with('company')->chunk(100, function($jobs) use ($file) {
                        foreach ($jobs as $job) {
                            fputcsv($file, [
                                $job->id,
                                $job->job_title,
                                $job->company->company_name ?? 'N/A',
                                $job->status,
                                $job->moderation_status ?? 'N/A',
                                $job->posted_date ? $job->posted_date->format('Y-m-d H:i:s') : 'N/A',
                            ]);
                        }
                    });
                    break;
                    
                case 'applications':
                    fputcsv($file, ['ID', 'Job Seeker', 'Job Title', 'Status', 'Applied Date']);
                    Application::with(['jobSeeker.user', 'jobPosting'])->chunk(100, function($applications) use ($file) {
                        foreach ($applications as $app) {
                            fputcsv($file, [
                                $app->id,
                                $app->jobSeeker ? ($app->jobSeeker->first_name . ' ' . $app->jobSeeker->last_name) : 'N/A',
                                $app->jobPosting->job_title ?? 'N/A',
                                $app->status,
                                $app->application_date ? $app->application_date->format('Y-m-d H:i:s') : 'N/A',
                            ]);
                        }
                    });
                    break;
                    
                case 'payments':
                    fputcsv($file, ['ID', 'Company', 'Amount', 'Status', 'Payment Date', 'Created At']);
                    PaymentTransaction::with('companySubscription.company')->chunk(100, function($payments) use ($file) {
                        foreach ($payments as $payment) {
                            fputcsv($file, [
                                $payment->id,
                                $payment->companySubscription->company->company_name ?? 'N/A',
                                number_format($payment->amount, 2),
                                $payment->status,
                                $payment->payment_date ? $payment->payment_date->format('Y-m-d H:i:s') : 'N/A',
                                $payment->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }
}
