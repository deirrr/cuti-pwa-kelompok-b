<?php

namespace Database\Seeders;

use App\Models\JenisCuti;
use Illuminate\Database\Seeder;

class JenisCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisCuti::query()->updateOrCreate(
            ['kode' => 'TAHUNAN'],
            [
                'nama' => 'Cuti Tahunan',
                'deskripsi' => 'Data awal untuk pengembangan; sesuaikan dengan kebijakan yang berlaku.',
                'jatah_bawaan' => 12,
                'mengurangi_saldo' => true,
                'aktif' => true,
            ],
        );
    }
}
