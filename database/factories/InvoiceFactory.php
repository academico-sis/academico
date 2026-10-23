<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'client_name' => fake()->name(),
            'client_idnumber' => fake()->numerify('########'),
            'client_address' => fake()->address(),
            'client_email' => fake()->safeEmail(),
            'client_phone' => fake()->phoneNumber(),
            'receipt_number' => fake()->unique()->numerify('INV-####'),
        ];
    }
}
