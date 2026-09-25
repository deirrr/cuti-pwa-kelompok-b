<?php

namespace Database\Factories;

use App\Enums\KeputusanPersetujuan;
use App\Enums\TahapPersetujuan;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use App\Models\PersetujuanCuti;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersetujuanCuti>
 */
class PersetujuanCutiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pengajuan_cuti_id' => PengajuanCuti::factory(),
            'pemberi_keputusan_id' => Pengguna::factory()->atasan(),
            'tahap' => TahapPersetujuan::Atasan,
            'keputusan' => KeputusanPersetujuan::Disetujui,
            'catatan' => null,
            'diputuskan_pada' => now(),
        ];
    }

    public function ditolak(string $catatan = 'Pengajuan tidak dapat disetujui.'): static
    {
        return $this->state(fn (array $attributes): array => [
            'keputusan' => KeputusanPersetujuan::Ditolak,
            'catatan' => $catatan,
        ]);
    }

    public function tahapAdminHr(): static
    {
        return $this->state(fn (array $attributes): array => [
            'pemberi_keputusan_id' => Pengguna::factory()->adminHr(),
            'tahap' => TahapPersetujuan::AdminHr,
        ]);
    }
}
