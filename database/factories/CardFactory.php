<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => Str::random(),
            'user_id' => null,
            'content' => '{"time":' . $this->faker->unixTime . ',"blocks":[{"id":"-' . Str::random(9) .'","type":"paragraph","data":{"text":"' . $this->faker->text() . '"}}],"version":"2.30.5"}',
            'stage' => $this->faker->numberBetween(1, 8),
            'next_repeat_at' => $this->faker->dateTimeBetween('-2 weeks', '+2 weeks'),
        ];
    }
}
