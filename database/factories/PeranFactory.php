<?php

namespace Database\Factories;

use App\Models\Peran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Peran>
 */
class PeranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->bothify('peran_###'),
            'nama' => fake()->unique()->words(2, true),
            'aktif' => true,
        ];
    }
}
