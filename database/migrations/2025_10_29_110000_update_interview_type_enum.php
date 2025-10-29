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
            DB::statement("ALTER TABLE interviews MODIFY interview_type ENUM('online','offline','phone_screen','phone','video','in_person') NOT NULL");
            return;
        }

        if ($driver === 'pgsql') {
            $values = ['online', 'offline', 'phone_screen'];

            foreach ($values as $value) {
                DB::statement(<<<SQL
DO $$
BEGIN
    ALTER TYPE interviews_interview_type_enum ADD VALUE IF NOT EXISTS '{$value}';
EXCEPTION WHEN duplicate_object THEN
    NULL;
END
$$;
SQL);
            }

            return;
        }

        // SQLite doesn't support ALTER COLUMN for ENUM, so we need to recreate the table
        if ($driver === 'sqlite') {
            // SQLite workaround: Create new table with updated enum values
            DB::statement('PRAGMA foreign_keys=off');
            
            // Create temporary table with new schema
            DB::statement("
                CREATE TABLE interviews_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    id_application INTEGER NOT NULL,
                    interviewer_name TEXT,
                    interview_date DATETIME NOT NULL,
                    interview_type TEXT NOT NULL CHECK(interview_type IN ('online','offline','phone_screen','phone','video','in_person')),
                    location TEXT,
                    notes TEXT,
                    created_at DATETIME,
                    updated_at DATETIME,
                    deleted_at DATETIME,
                    FOREIGN KEY (id_application) REFERENCES applications(id) ON DELETE CASCADE
                )
            ");
            
            // Copy data from old table
            DB::statement("INSERT INTO interviews_new SELECT * FROM interviews");
            
            // Drop old table
            DB::statement("DROP TABLE interviews");
            
            // Rename new table
            DB::statement("ALTER TABLE interviews_new RENAME TO interviews");
            
            DB::statement('PRAGMA foreign_keys=on');
            
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
            DB::statement("ALTER TABLE interviews MODIFY interview_type ENUM('phone','video','in_person') NOT NULL");
        }

        // PostgreSQL enumerations cannot remove values easily; no-op for other drivers.
    }
};

