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
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Menambahkan kolom is_active (defaultnya aktif/true) setelah kolom duration_days
            $table->boolean('is_active')->default(true)->after('duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Saat di-rollback, kolom is_active akan dihapus
            $table->dropColumn('is_active');
        });
    }
};