<?php

namespace Database\Factories;

use App\Enums\KategoriJabatan;
use App\Models\Jabatan;
use App\Models\UnitBisnis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jabatan>
 */
class JabatanFactory extends Factory
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
            'departemen_id' => null,
            'atasan_jabatan_id' => null,
            'kode' => fake()->unique()->bothify('JBT-####'),
            'nama' => fake()->jobTitle(),
            'kategori' => KategoriJabatan::Staf,
            'langsung_ke_hr' => false,
            'aktif' => true,
        ];
    }
}
