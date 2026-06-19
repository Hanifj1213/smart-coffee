<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionDetail>
 */
class TransactionDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'nama_menu' => fake()->words(2, true),
            'kategori_menu' => fake()->randomElement(['Coffee', 'Non-Coffee', 'Food']),
            'rasa_manis' => fake()->numberBetween(1, 5),
            'harga' => fake()->numberBetween(18000, 40000),
        ];
    }
}
