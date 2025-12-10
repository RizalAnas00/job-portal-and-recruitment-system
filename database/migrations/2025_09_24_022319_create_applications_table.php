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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_job_seeker');
            $table->unsignedBigInteger('id_job_posting');
            $table->timestamp('application_date')->useCurrent();
            $table->enum('status', ['pending', 'reviewed', 'accepted', 'rejected'])->default('pending');
            $table->text('cover_letter')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_job_seeker')
                ->references('id')->on('job_seekers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_job_posting')
                ->references('id')->on('job_postings')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->index(['id_job_seeker', 'id_job_posting']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};