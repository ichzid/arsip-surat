<?php

namespace Database\Factories;

use App\Models\OutgoingLetter;
use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutgoingLetterFactory extends Factory
{
    protected $model = OutgoingLetter::class;

    public function definition(): array
    {
        return [
            'mail_number' => fake()->bothify('OUT/???/###/2024'),
            'recipient' => fake()->company(),
            'mail_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'subject' => fake()->sentence(6),
            'division_id' => Division::inRandomOrder()->first()->id ?? 1,
            'created_by' => User::inRandomOrder()->first()->id ?? 1,
            'file_path' => null,
        ];
    }
}
