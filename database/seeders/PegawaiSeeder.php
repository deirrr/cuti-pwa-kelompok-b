<?php

namespace Database\Seeders;

use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
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
        $bagianPendaftaran = BagianOrganisasi::query()->where('kode', 'PENDAFTARAN')->firstOrFail();
        $departemenSdm = BagianOrganisasi::query()->where('kode', 'SDM')->firstOrFail();

        $akunAtasan = Pengguna::query()->where('email', 'atasan@example.test')->firstOrFail();
        $atasan = Pegawai::query()->updateOrCreate(
            ['pengguna_id' => $akunAtasan->getKey()],
            [
                'bagian_organisasi_id' => $bagianPendaftaran->getKey(),
                'atasan_id' => null,
                'nomor_induk' => 'PGW-000001',
                'nama' => 'Atasan Contoh',
                'tanggal_masuk' => now()->subYears(5)->toDateString(),
                'aktif' => true,
            ],
        );

        $dataPegawai = [
            [
                'email' => 'admin.hr@example.test',
                'bagian_organisasi_id' => $departemenSdm->getKey(),
                'nomor_induk' => 'PGW-000002',
                'nama' => 'Admin HR Contoh',
            ],
            [
                'email' => 'admin.hr.2@example.test',
                'bagian_organisasi_id' => $departemenSdm->getKey(),
                'nomor_induk' => 'PGW-000003',
                'nama' => 'Admin HR Kedua',
            ],
            [
                'email' => 'karyawan@example.test',
                'bagian_organisasi_id' => $bagianPendaftaran->getKey(),
                'nomor_induk' => 'PGW-000004',
                'nama' => 'Karyawan Contoh',
            ],
        ];

        foreach ($dataPegawai as $data) {
            $pengguna = Pengguna::query()->where('email', $data['email'])->firstOrFail();

            Pegawai::query()->updateOrCreate(
                ['pengguna_id' => $pengguna->getKey()],
                [
                    'bagian_organisasi_id' => $data['bagian_organisasi_id'],
                    'atasan_id' => $pengguna->peran === PeranPengguna::Karyawan ? $atasan->getKey() : null,
                    'nomor_induk' => $data['nomor_induk'],
                    'nama' => $data['nama'],
                    'tanggal_masuk' => now()->subYears(2)->toDateString(),
                    'aktif' => true,
                ],
            );
        }
    }
}
