<?php

namespace Database\Seeders;

use App\Models\JenisCuti;
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
        $jenisCuti = JenisCuti::query()->where('kode', 'TAHUNAN')->firstOrFail();

        Pegawai::query()->each(function (Pegawai $pegawai) use ($jenisCuti): void {
            SaldoCuti::query()->updateOrCreate(
                [
                    'pegawai_id' => $pegawai->getKey(),
                    'jenis_cuti_id' => $jenisCuti->getKey(),
                    'tahun' => now()->year,
                ],
                [
                    'jatah_awal' => $jenisCuti->jatah_bawaan,
                    'saldo_tersedia' => $jenisCuti->jatah_bawaan,
                    'catatan' => 'Saldo awal data pengembangan.',
                ],
            );
        });
    }
}
