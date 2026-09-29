<?php

namespace Database\Factories;

use App\Enums\JenisBagianOrganisasi;
use App\Models\BagianOrganisasi;
use App\Models\UnitBisnis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BagianOrganisasi>
 */
class BagianOrganisasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_bisnis_id' => UnitBisnis::factory(),
            'induk_id' => null,
            'jenis' => JenisBagianOrganisasi::Bagian,
            'kode' => fake()->unique()->bothify('BGN-###'),
            'nama' => 'Bagian '.fake()->unique()->words(2, true),
            'aktif' => true,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes): array => [
            'aktif' => false,
        ]);
    }
}
