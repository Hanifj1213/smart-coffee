<?php

namespace Database\Factories;

use App\Models\Reward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reward>
 */
class RewardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->words(2, true),
            'deskripsi' => fake()->sentence(),
            'kategori' => fake()->randomElement(['Produk', 'Voucher', 'Merchandise']),
            'poin_cost' => fake()->numberBetween(500, 10000),
            'stok' => fake()->randomElement([null, 5, 10, 50]),
            'icon' => fake()->randomElement(['🎁', '☕', '🍰', '🎫', '🥤']),
            'is_active' => true,
        ];
    }
}
