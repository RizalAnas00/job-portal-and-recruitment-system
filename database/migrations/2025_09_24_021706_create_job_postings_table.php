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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id(); // id (BIGINT)
            $table->unsignedBigInteger('id_company'); // FK to companies.id (not null)
            $table->string('job_title');
            $table->text('job_description')->nullable();
            $table->string('location'); // not null
            $table->enum('job_type', ['full_time', 'part_time', 'contract', 'internship', 'temporary', 'freelance', 'remote'])->default('full_time');
            $table->string('salary_range')->nullable();
            $table->timestamp('posted_date')->useCurrent();
            $table->timestamp('closing_date')->nullable();
            $table->enum('status', ['draft', 'open', 'paused', 'closed', 'archived'])->default('open');
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
            
            $table->foreign('id_company')
                ->references('id')->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->index(['id_company', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};