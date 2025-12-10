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
            // tambahkan timestamps jika belum ada
            if (!Schema::hasColumn('job_postings', 'created_at')) {
                $table->timestamps();
            }

            // tambahkan softDeletes jika belum ada
            if (!Schema::hasColumn('job_postings', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            // hapus softDeletes jika ada
            if (Schema::hasColumn('job_postings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            // hapus timestamps jika ada
            if (Schema::hasColumn('job_postings', 'created_at') && Schema::hasColumn('job_postings', 'updated_at')) {
                $table->dropTimestamps();
            }
        });
    }
};