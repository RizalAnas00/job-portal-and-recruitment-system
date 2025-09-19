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
            $table->string('company_name', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->text('company_description');
            $table->string('website');
            $table->string('industry');
            $table->text('address');
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
