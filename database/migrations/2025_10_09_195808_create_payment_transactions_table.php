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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('id_company_subscription')->constrained('company_subscriptions')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('va_number')->nullable();
            $table->string('payment_url')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
