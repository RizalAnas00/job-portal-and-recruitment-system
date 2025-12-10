<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class sendEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start = now();

        Log::info('email sending at : '. $start->toDateTimeString());

        sleep(4);
        $end = now();
        Log::info('email sent at : '. $end->toDateTimeString());
    }
}
