<?php

namespace Database\Seeders;

use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akun = [
            ['email' => 'admin.hr@example.test', 'peran' => PeranPengguna::AdminHr],
            ['email' => 'admin.hr.2@example.test', 'peran' => PeranPengguna::AdminHr],
            ['email' => 'atasan@example.test', 'peran' => PeranPengguna::Atasan],
            ['email' => 'karyawan@example.test', 'peran' => PeranPengguna::Karyawan],
        ];

        foreach ($akun as $dataAkun) {
            Pengguna::query()->updateOrCreate(
                ['email' => $dataAkun['email']],
                [
                    'kata_sandi' => 'password',
                    'peran' => $dataAkun['peran'],
                    'aktif' => true,
                ],
            );
        }
    }
}
