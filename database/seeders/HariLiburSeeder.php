<?php

namespace Database\Seeders;

use App\Enums\JenisHariLibur;
use App\Models\HariLibur;
use Illuminate\Database\Seeder;

class HariLiburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hariLiburNasional = [
            ['tanggal' => '2026-01-01', 'nama' => 'Tahun Baru 2026 Masehi'],
            ['tanggal' => '2026-01-16', 'nama' => 'Isra Mikraj Nabi Muhammad SAW'],
            ['tanggal' => '2026-02-17', 'nama' => 'Tahun Baru Imlek 2577 Kongzili'],
            ['tanggal' => '2026-03-19', 'nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)'],
            ['tanggal' => '2026-03-21', 'nama' => 'Hari Raya Idul Fitri 1447 H'],
            ['tanggal' => '2026-03-22', 'nama' => 'Hari Raya Idul Fitri 1447 H'],
            ['tanggal' => '2026-04-03', 'nama' => 'Wafat Yesus Kristus'],
            ['tanggal' => '2026-04-05', 'nama' => 'Hari Kebangkitan Yesus Kristus (Paskah)'],
            ['tanggal' => '2026-05-01', 'nama' => 'Hari Buruh Internasional'],
            ['tanggal' => '2026-05-14', 'nama' => 'Kenaikan Yesus Kristus'],
            ['tanggal' => '2026-05-27', 'nama' => 'Hari Raya Idul Adha 1447 H'],
            ['tanggal' => '2026-05-31', 'nama' => 'Hari Raya Waisak 2570 BE'],
            ['tanggal' => '2026-06-01', 'nama' => 'Hari Lahir Pancasila'],
            ['tanggal' => '2026-06-16', 'nama' => '1 Muharam 1448 H (Tahun Baru Islam)'],
            ['tanggal' => '2026-08-17', 'nama' => 'Hari Proklamasi Kemerdekaan'],
            ['tanggal' => '2026-08-25', 'nama' => 'Maulid Nabi Muhammad SAW'],
            ['tanggal' => '2026-12-25', 'nama' => 'Kelahiran Yesus Kristus'],
        ];

        foreach ($hariLiburNasional as $hariLibur) {
            HariLibur::query()->updateOrCreate(
                ['tanggal' => $hariLibur['tanggal']],
                [
                    'nama' => $hariLibur['nama'],
                    'jenis' => JenisHariLibur::Nasional,
                    'aktif' => true,
                ],
            );
        }
    }
}
