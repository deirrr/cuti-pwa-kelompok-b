<?php

namespace Database\Factories;

use App\Models\JenisCuti;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JenisCuti>
 */
class JenisCutiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->bothify('JC-###'),
            'nama' => 'Jenis Cuti '.fake()->unique()->words(2, true),
            'deskripsi' => fake()->sentence(),
            'jatah_bawaan' => 12,
            'mengurangi_saldo' => true,
            'aktif' => true,
        ];
    }

    public function tidakMengurangiSaldo(): static
    {
        return $this->state(fn (array $attributes): array => [
            'mengurangi_saldo' => false,
            'jatah_bawaan' => 0,
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes): array => [
            'aktif' => false,
        ]);
    }
}
