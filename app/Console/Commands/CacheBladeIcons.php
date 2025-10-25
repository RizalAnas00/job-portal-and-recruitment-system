<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CacheBladeIcons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:icons-cache
                            {--skip-view : Skip clearing views}
                            {--skip-clear : Skip clearing old icons cache}
                            {--skip-cache : Skip creating new icons cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear views, clear old icon cache, then rebuild Blade Icons cache (with optional skips).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();
        $this->info('🧩 Starting Blade Icons cache process...');
        $this->newLine();

        // Step 1: Clear views (optional)
        if (! $this->option('skip-view')) {
            $this->info('Clearing old views...');
            Artisan::call('view:clear');
            $this->line(Artisan::output());
        } else {
            $this->warn('⏭ Skipped view:clear');
        }

        // Step 2: Clear old icons cache (optional)
        if (! $this->option('skip-clear')) {
            $this->info('Clearing old icons cache...');
            Artisan::call('icons:clear');
            $this->line(Artisan::output());
        } else {
            $this->warn('⏭ Skipped icons:clear');
        }

        // Step 3: Rebuild new icons cache (optional)
        if (! $this->option('skip-cache')) {
            $this->info('Building new Blade Icons cache...');
            Artisan::call('icons:cache');
            $this->line(Artisan::output());
        } else {
            $this->warn('⏭ Skipped icons:cache');
        }

        $this->newLine();
        $this->info('Blade Icons cache process complete!');
    }
}
