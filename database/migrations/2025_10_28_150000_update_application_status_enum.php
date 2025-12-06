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

            DB::statement("
                UPDATE applications
                SET status='pending'
                WHERE status NOT IN (
                    'pending','reviewed','accepted','rejected',
                    'applied','under_review','interview_scheduled',
                    'interviewing','offered','hired'
                ) OR status IS NULL OR status=''
            ");

            DB::statement("
                ALTER TABLE applications MODIFY status ENUM(
                    'pending','reviewed','accepted','rejected',
                    'applied','under_review','interview_scheduled',
                    'interviewing','offered','hired'
                ) DEFAULT 'pending'
            ");

            return;
        }

        if ($driver === 'pgsql') {
            $values = ['applied','under_review','interview_scheduled','interviewing','offered','hired'];

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

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');

            DB::statement("
                CREATE TABLE applications_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    id_job_seeker INTEGER NOT NULL,
                    id_job_posting INTEGER NOT NULL,
                    application_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                    status TEXT DEFAULT 'pending' NOT NULL CHECK(status IN (
                        'pending','reviewed','accepted','rejected',
                        'applied','under_review','interview_scheduled',
                        'interviewing','offered','hired'
                    )),
                    cover_letter TEXT,
                    created_at DATETIME,
                    updated_at DATETIME,
                    deleted_at DATETIME,
                    FOREIGN KEY (id_job_seeker) REFERENCES job_seekers(id),
                    FOREIGN KEY (id_job_posting) REFERENCES job_postings(id)
                )
            ");

            DB::statement("INSERT INTO applications_new SELECT * FROM applications");
            DB::statement("DROP TABLE applications");
            DB::statement("ALTER TABLE applications_new RENAME TO applications");
            DB::statement('PRAGMA foreign_keys=on');
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {

            // 🔥 convert semua status baru menjadi pending dulu
            DB::statement("
                UPDATE applications
                SET status='pending'
                WHERE status NOT IN ('pending','reviewed','accepted','rejected');
            ");

            DB::statement("
                ALTER TABLE applications MODIFY status ENUM(
                    'pending','reviewed','accepted','rejected'
                ) DEFAULT 'pending'
            ");
        }
        
        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE applications MODIFY status ENUM(
                    'pending','reviewed','accepted','rejected'
                ) DEFAULT 'pending'
            ");
        }

    }
};
