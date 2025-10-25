<?php

namespace Database\Factories;

use App\Models\CompanySubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id_company_subscription = CompanySubscription::all()->random()->id;
        $vaNumber = $this->faker->bankAccountNumber();

        return [
            'id_company_subscription' => $id_company_subscription,
            'amount' => $this->faker->randomFloat(2, 29999, 109000),
            'payment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'payment_method' => $this->faker->randomElement(['bri', 'dana', 'paypal', 'mandiri', 'btn', 'bca', 'bni']),
            'va_number' => $vaNumber,
            'payment_url' => 'https://payment.example.com/pay/' . $vaNumber,
            'expires_at' => $this->faker->dateTimeBetween('now', '+24 hours'),
            'status' => $this->faker->randomElement(['pending', 'success', 'failed']),
        ];
    }
}
