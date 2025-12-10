<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class QueueMonitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        // Get failed jobs
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->paginate(15);

        // Get approximate count of pending jobs (standard table)
        // Adjust table name if you use a custom table in config
        $pendingJobsCount = DB::table('jobs')->count();

        return view('admin.monitoring.queue', compact('failedJobs', 'pendingJobsCount'));
    }

    public function retry($id)
    {
        try {
            Artisan::call('queue:retry', ['id' => $id]);
            return back()->with('success', 'Job has been queued for retry.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to retry job: ' . $e->getMessage());
        }
    }

    public function retryAll()
    {
        try {
            Artisan::call('queue:retry', ['id' => 'all']);
            return back()->with('success', 'All failed jobs have been queued for retry.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to retry jobs: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            Artisan::call('queue:forget', ['id' => $id]);
            return back()->with('success', 'Job removed from failed queue.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove job: ' . $e->getMessage());
        }
    }

    public function flush()
    {
        try {
            Artisan::call('queue:flush');
            return back()->with('success', 'All failed jobs have been removed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to flush jobs: ' . $e->getMessage());
        }
    }
}
