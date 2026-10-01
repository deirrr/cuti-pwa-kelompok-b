<?php

namespace Database\Factories;

use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pegawai>
 */
class PegawaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pengguna_id' => Pengguna::factory(),
            'bagian_organisasi_id' => BagianOrganisasi::factory(),
            'atasan_id' => null,
            'nomor_induk' => fake()->unique()->numerify('PGW-######'),
            'nama' => fake()->name(),
            'tanggal_masuk' => fake()->dateTimeBetween('-10 years', '-1 month')->format('Y-m-d'),
            'jatah_cuti' => 12,
            'aktif' => true,
        ];
    }

    public function denganAtasan(Pegawai $atasan): static
    {
        return $this->state(fn (array $attributes): array => [
            'atasan_id' => $atasan->getKey(),
            'bagian_organisasi_id' => $atasan->bagian_organisasi_id,
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes): array => [
            'aktif' => false,
        ]);
    }
}
