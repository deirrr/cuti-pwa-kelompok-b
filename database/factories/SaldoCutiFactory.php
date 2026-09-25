<?php

namespace Database\Factories;

use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaldoCuti>
 */
class SaldoCutiFactory extends Factory
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
            'jenis_cuti_id' => JenisCuti::factory(),
            'tahun' => now()->year,
            'jatah_awal' => 12,
            'saldo_tersedia' => 12,
            'catatan' => null,
        ];
    }
}
