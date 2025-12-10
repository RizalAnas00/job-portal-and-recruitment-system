<?php

namespace Database\Seeders;

use App\Models\PaymentTransaction;
use DragonCode\Contracts\Cashier\Config\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentTransaction::factory()->count(10)->create();
    }
}
