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
        Schema::create('job_posting_skill', function (Blueprint $table) {
            $table->unsignedBigInteger('id_job_posting');
            $table->unsignedBigInteger('id_skill');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['id_job_posting', 'id_skill']);

            $table->foreign('id_job_posting')
                ->references('id')->on('job_postings')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_skill')
                ->references('id')->on('skills')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->index(['id_job_posting']);
            $table->index(['id_skill']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posting_skill');
    }
};