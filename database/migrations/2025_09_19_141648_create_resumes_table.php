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
        Schema::create('resumes', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignId('job_seeker_id')
                  ->constrained('job_seekers')
                  ->cascadeOnDelete();
            $table->string('resume_title');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('upload_date')->useCurrent();
            $table->text('parsed_text')->nullable();
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
