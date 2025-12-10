<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add composite indexes for the most common query patterns
            // This optimizes: WHERE id_job_seeker = ? AND id_company IS NULL ORDER BY created_at DESC
            $table->index(['id_job_seeker', 'id_company', 'created_at'], 'idx_jobseeker_company_created');
            
            // This optimizes: WHERE id_company = ? AND id_job_seeker IS NULL ORDER BY created_at DESC
            $table->index(['id_company', 'id_job_seeker', 'created_at'], 'idx_company_jobseeker_created');
            
            // This optimizes: WHERE is_read = false AND id_job_seeker = ? ORDER BY created_at DESC
            $table->index(['is_read', 'id_job_seeker', 'created_at'], 'idx_read_jobseeker_created');
            
            // This optimizes: WHERE is_read = false AND id_company = ? ORDER BY created_at DESC
            $table->index(['is_read', 'id_company', 'created_at'], 'idx_read_company_created');
        });

        // Add CHECK constraint to ensure exactly one recipient is set
        // Note: SQLite has limited CHECK constraint support, MySQL and PostgreSQL support this better
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('
                ALTER TABLE notifications 
                ADD CONSTRAINT chk_notification_recipient 
                CHECK (
                    (id_job_seeker IS NOT NULL AND id_company IS NULL) 
                    OR 
                    (id_job_seeker IS NULL AND id_company IS NOT NULL)
                )
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_jobseeker_company_created');
            $table->dropIndex('idx_company_jobseeker_created');
            $table->dropIndex('idx_read_jobseeker_created');
            $table->dropIndex('idx_read_company_created');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE notifications DROP CONSTRAINT chk_notification_recipient');
        }
    }
};
