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
        Schema::create('companies', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('phone_number', 15)->unique();
            $table->string('company_name', 100);
            $table->text('company_description');
            $table->string('website');
            $table->string('industry');
            $table->text('address');
            $table->string('logo_path')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
