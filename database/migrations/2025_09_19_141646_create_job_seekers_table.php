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
        Schema::create('job_seekers', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->text('profile_summary')->nullable();
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seekers');
    }
};
