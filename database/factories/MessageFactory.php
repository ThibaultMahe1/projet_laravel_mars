<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => \App\Models\User::factory(),
            'target_dome' => fake()->randomElement(['A', 'B']),
            'content' => fake()->paragraph(),
            'metadata' => ['priority' => fake()->randomElement(['normal', 'high', 'critical']), 'coordinates' => fake()->latitude() . ',' . fake()->longitude()],
        ];
    }
}
