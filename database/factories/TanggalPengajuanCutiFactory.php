<?php

namespace Database\Factories;

use App\Models\PengajuanCuti;
use App\Models\TanggalPengajuanCuti;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TanggalPengajuanCuti>
 */
class TanggalPengajuanCutiFactory extends Factory
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
            'tanggal' => fake()->unique()->dateTimeBetween('+1 day', '+1 year')->format('Y-m-d'),
        ];
    }
}
