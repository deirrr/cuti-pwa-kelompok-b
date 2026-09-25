<?php

namespace Database\Factories;

use App\Enums\StatusPengajuanCuti;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengajuanCuti>
 */
class PengajuanCutiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_pengajuan' => sprintf(
                'CUTI-%d-%s',
                now()->year,
                fake()->unique()->numerify('######'),
            ),
            'pegawai_id' => Pegawai::factory(),
            'jenis_cuti_id' => JenisCuti::factory(),
            'status' => StatusPengajuanCuti::Draf,
            'alasan' => fake()->sentence(),
            'jumlah_hari' => 0,
            'diajukan_pada' => null,
            'dibatalkan_pada' => null,
        ];
    }

    public function menungguAtasan(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => StatusPengajuanCuti::MenungguAtasan,
            'diajukan_pada' => now(),
        ]);
    }

    public function menungguHr(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => StatusPengajuanCuti::MenungguHr,
            'diajukan_pada' => now(),
        ]);
    }

    public function disetujui(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => StatusPengajuanCuti::Disetujui,
            'diajukan_pada' => now(),
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => StatusPengajuanCuti::Ditolak,
            'diajukan_pada' => now(),
        ]);
    }

    public function dibatalkan(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => StatusPengajuanCuti::Dibatalkan,
            'dibatalkan_pada' => now(),
        ]);
    }
}
