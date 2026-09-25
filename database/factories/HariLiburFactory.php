<?php

namespace Database\Factories;

use App\Enums\JenisHariLibur;
use App\Models\HariLibur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HariLibur>
 */
class HariLiburFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tanggal' => fake()->unique()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            'nama' => fake()->words(3, true),
            'jenis' => JenisHariLibur::Nasional,
            'aktif' => true,
        ];
    }

    public function perusahaan(): static
    {
        return $this->state(fn (array $attributes): array => [
            'jenis' => JenisHariLibur::Perusahaan,
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes): array => [
            'aktif' => false,
        ]);
    }
}
