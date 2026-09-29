<?php

namespace Database\Seeders;

use App\Enums\PeranPengguna;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Illuminate\Database\Seeder;

class SaldoCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::query()
            ->whereHas('pengguna', fn ($query) => $query->whereIn('peran', [
                PeranPengguna::Karyawan->value,
                PeranPengguna::Atasan->value,
            ]))
            ->each(function (Pegawai $pegawai): void {
                SaldoCuti::query()->updateOrCreate(
                    [
                        'pegawai_id' => $pegawai->getKey(),
                        'tahun' => now()->year,
                    ],
                    [
                        'jatah_awal' => 12,
                        'saldo_tersedia' => 12,
                        'catatan' => 'Saldo awal data pengembangan.',
                    ],
                );
            });
    }
}
