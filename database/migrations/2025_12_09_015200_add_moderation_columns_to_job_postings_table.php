<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->enum('moderation_status', ['pending', 'approved', 'rejected'])
                  ->default('pending') // Default pending agar tidak langsung live
                  ->after('status');
            
            // Alasan jika ditolak
            $table->text('rejection_reason')->nullable()->after('moderation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn(['moderation_status', 'rejection_reason']);
        });
    }
};
