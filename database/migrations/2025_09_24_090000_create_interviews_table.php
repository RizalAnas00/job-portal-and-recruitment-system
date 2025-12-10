<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_application')->constrained('applications')->onDelete('cascade');
            $table->string('interviewer_name')->nullable();
            $table->timestamp('interview_date');
            $table->enum('interview_type', ['phone', 'video', 'in_person']);
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};