<?php

namespace Database\Factories;

use App\Enums\KategoriUnitBisnis;
use App\Models\UnitBisnis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitBisnis>
 */
class UnitBisnisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->bothify('UNIT-###'),
            'nama' => 'Unit '.fake()->unique()->words(2, true),
            'kategori' => KategoriUnitBisnis::Operasional,
            'aktif' => true,
        ];
    }
}
