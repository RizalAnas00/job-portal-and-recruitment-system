<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE applications MODIFY status ENUM('pending','reviewed','accepted','rejected','applied','under_review','interview_scheduled','interviewing','offered','hired') DEFAULT 'pending'");
            return;
        }

        if ($driver === 'pgsql') {
            $values = ['applied', 'under_review', 'interview_scheduled', 'interviewing', 'offered', 'hired'];

            foreach ($values as $value) {
                DB::statement(<<<SQL
DO $$
BEGIN
    ALTER TYPE applications_status_enum ADD VALUE IF NOT EXISTS '{$value}';
EXCEPTION WHEN duplicate_object THEN
    NULL;
END
$$;
SQL);
            }

            return;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE applications MODIFY status ENUM('pending','reviewed','accepted','rejected') DEFAULT 'pending'");
        }

        // PostgreSQL enumerations cannot remove values easily; no-op for other drivers.
    }
};


