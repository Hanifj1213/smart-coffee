<?php

namespace Database\Factories;

use App\Models\CrmNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmNotification>
 */
class CrmNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['WhatsApp', 'Email']),
            'message' => fake()->sentence(),
        ];
    }
}
