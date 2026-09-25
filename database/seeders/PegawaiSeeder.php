<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemenOperasional = Departemen::query()->where('kode', 'OPS')->firstOrFail();
        $departemenSdm = Departemen::query()->where('kode', 'SDM')->firstOrFail();

        $akunAtasan = Pengguna::query()->where('email', 'atasan@example.test')->firstOrFail();
        $atasan = Pegawai::query()->updateOrCreate(
            ['pengguna_id' => $akunAtasan->getKey()],
            [
                'departemen_id' => $departemenOperasional->getKey(),
                'atasan_id' => null,
                'nomor_induk' => 'PGW-000001',
                'nama' => 'Atasan Contoh',
                'jabatan' => 'Kepala Operasional',
                'tanggal_masuk' => now()->subYears(5)->toDateString(),
                'aktif' => true,
            ],
        );

        $dataPegawai = [
            [
                'email' => 'admin.hr@example.test',
                'departemen_id' => $departemenSdm->getKey(),
                'nomor_induk' => 'PGW-000002',
                'nama' => 'Admin HR Contoh',
                'jabatan' => 'Staf HR',
            ],
            [
                'email' => 'admin.hr.2@example.test',
                'departemen_id' => $departemenSdm->getKey(),
                'nomor_induk' => 'PGW-000003',
                'nama' => 'Admin HR Kedua',
                'jabatan' => 'Staf HR',
            ],
            [
                'email' => 'karyawan@example.test',
                'departemen_id' => $departemenOperasional->getKey(),
                'nomor_induk' => 'PGW-000004',
                'nama' => 'Karyawan Contoh',
                'jabatan' => 'Staf Operasional',
            ],
        ];

        foreach ($dataPegawai as $data) {
            $pengguna = Pengguna::query()->where('email', $data['email'])->firstOrFail();

            Pegawai::query()->updateOrCreate(
                ['pengguna_id' => $pengguna->getKey()],
                [
                    'departemen_id' => $data['departemen_id'],
                    'atasan_id' => $atasan->getKey(),
                    'nomor_induk' => $data['nomor_induk'],
                    'nama' => $data['nama'],
                    'jabatan' => $data['jabatan'],
                    'tanggal_masuk' => now()->subYears(2)->toDateString(),
                    'aktif' => true,
                ],
            );
        }
    }
}
