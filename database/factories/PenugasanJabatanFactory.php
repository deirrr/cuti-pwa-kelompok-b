<?php

namespace Database\Factories;

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PenugasanJabatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PenugasanJabatan>
 */
class PenugasanJabatanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pegawai_id' => Pegawai::factory(),
            'jabatan_id' => Jabatan::factory(),
            'utama' => false,
            'tanggal_mulai' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'tanggal_selesai' => null,
            'aktif' => true,
        ];
    }

    public function utama(): static
    {
        return $this->state(fn (array $attributes): array => [
            'utama' => true,
        ]);
    }
}
