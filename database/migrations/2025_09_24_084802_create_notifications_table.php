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
        Schema::create('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('id_job_seeker')->nullable();
            $table->unsignedBigInteger('id_company')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('link_url')->nullable();

            // relasi ke job_seeker
            $table->foreign('id_job_seeker')
                  ->references('id')->on('job_seekers')
                  ->onDelete('cascade');

            // relasi ke company
            $table->foreign('id_company')
                  ->references('id')->on('companies')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
