<?php

namespace Database\Factories;

use App\Models\IncomingLetter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomingLetterFactory extends Factory
{
    protected $model = IncomingLetter::class;

    public function definition(): array
    {
        return [
            'agenda_number' => 'AGN-' . date('Ymd') . '-' . fake()->unique()->numerify('####'),
            'mail_number' => fake()->bothify('###/???/IV/2024'),
            'mail_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'received_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'origin' => fake()->company(),
            'subject' => fake()->sentence(6),
            'status' => fake()->randomElement(['new', 'disposition', 'done']),
            'created_by' => User::role('Operator Divisi Umum')->inRandomOrder()->first()->id ?? 1,
            'file_path' => null,
        ];
    }
}
