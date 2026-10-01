<?php

namespace Database\Seeders;

use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\SaldoCuti;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelompokKaryawan = [
            'IF' => [
                'atasan' => ['nomor_induk' => 'IF001', 'nama' => 'Rina Puspitasari', 'email' => 'if.atasan@example.test'],
                'staff' => [
                    ['nomor_induk' => 'IF002', 'nama' => 'Dedi Kurniawan', 'email' => 'if.staff1@example.test'],
                    ['nomor_induk' => 'IF003', 'nama' => 'Siti Rahmawati', 'email' => 'if.staff2@example.test'],
                    ['nomor_induk' => 'IF004', 'nama' => 'Andi Pratama', 'email' => 'if.staff3@example.test'],
                ],
            ],
            'PERAWAT-GIGI' => [
                'atasan' => ['nomor_induk' => 'GIGI001', 'nama' => 'Maya Lestari', 'email' => 'gigi.atasan@example.test'],
                'staff' => [
                    ['nomor_induk' => 'GIGI002', 'nama' => 'Nabila Putri', 'email' => 'gigi.staff1@example.test'],
                    ['nomor_induk' => 'GIGI003', 'nama' => 'Rafi Maulana', 'email' => 'gigi.staff2@example.test'],
                    ['nomor_induk' => 'GIGI004', 'nama' => 'Intan Permata', 'email' => 'gigi.staff3@example.test'],
                ],
            ],
            'PERAWAT-SP' => [
                'atasan' => ['nomor_induk' => 'SP001', 'nama' => 'Dewi Anggraini', 'email' => 'sp.atasan@example.test'],
                'staff' => [
                    ['nomor_induk' => 'SP002', 'nama' => 'Fajar Hidayat', 'email' => 'sp.staff1@example.test'],
                    ['nomor_induk' => 'SP003', 'nama' => 'Nur Aisyah', 'email' => 'sp.staff2@example.test'],
                    ['nomor_induk' => 'SP004', 'nama' => 'Rizky Ramadhan', 'email' => 'sp.staff3@example.test'],
                    ['nomor_induk' => 'SP005', 'nama' => 'Putri Amelia', 'email' => 'sp.staff4@example.test'],
                    ['nomor_induk' => 'SP006', 'nama' => 'Yoga Saputra', 'email' => 'sp.staff5@example.test'],
                ],
            ],
            'MANAJEMEN' => [
                'atasan' => ['nomor_induk' => 'MGT001', 'nama' => 'Budi Santoso', 'email' => 'manajemen.atasan@example.test'],
                'staff' => [
                    ['nomor_induk' => 'MGT002', 'nama' => 'Lina Marlina', 'email' => 'manajemen.staff1@example.test'],
                    ['nomor_induk' => 'MGT003', 'nama' => 'Arif Nugraha', 'email' => 'manajemen.staff2@example.test'],
                ],
            ],
            'RM' => [
                'atasan' => ['nomor_induk' => 'RM001', 'nama' => 'Tika Handayani', 'email' => 'pendaftaran.atasan@example.test'],
                'staff' => [
                    ['nomor_induk' => 'RM002', 'nama' => 'Rani Oktaviani', 'email' => 'pendaftaran.staff1@example.test'],
                    ['nomor_induk' => 'RM003', 'nama' => 'Aldi Firmansyah', 'email' => 'pendaftaran.staff2@example.test'],
                    ['nomor_induk' => 'RM004', 'nama' => 'Wulan Sari', 'email' => 'pendaftaran.staff3@example.test'],
                    ['nomor_induk' => 'RM005', 'nama' => 'Reza Pahlevi', 'email' => 'pendaftaran.staff4@example.test'],
                ],
            ],
        ];

        DB::transaction(function () use ($kelompokKaryawan): void {
            $peran = Peran::query()
                ->whereIn('kode', [PeranPengguna::Atasan->value, PeranPengguna::Karyawan->value])
                ->get()
                ->keyBy('kode');

            foreach ($kelompokKaryawan as $kodeBagian => $kelompok) {
                $bagian = BagianOrganisasi::query()->where('kode', $kodeBagian)->firstOrFail();
                $atasan = $this->simpanKaryawan(
                    bagian: $bagian,
                    dataKaryawan: $kelompok['atasan'],
                    peranPengguna: PeranPengguna::Atasan,
                    peranId: $peran->get(PeranPengguna::Atasan->value)->getKey(),
                );

                foreach ($kelompok['staff'] as $dataStaff) {
                    $this->simpanKaryawan(
                        bagian: $bagian,
                        dataKaryawan: $dataStaff,
                        peranPengguna: PeranPengguna::Karyawan,
                        peranId: $peran->get(PeranPengguna::Karyawan->value)->getKey(),
                        atasan: $atasan,
                    );
                }
            }
        });
    }

    /**
     * @param  array{nomor_induk: string, nama: string, email: string}  $dataKaryawan
     */
    private function simpanKaryawan(
        BagianOrganisasi $bagian,
        array $dataKaryawan,
        PeranPengguna $peranPengguna,
        int $peranId,
        ?Pegawai $atasan = null,
    ): Pegawai {
        $pengguna = Pengguna::query()->updateOrCreate(
            ['email' => $dataKaryawan['email']],
            [
                'kata_sandi' => 'password',
                'peran' => $peranPengguna,
                'aktif' => true,
            ],
        );

        $pengguna->peranSistem()->sync([$peranId]);

        $pegawai = Pegawai::query()->updateOrCreate(
            ['pengguna_id' => $pengguna->getKey()],
            [
                'bagian_organisasi_id' => $bagian->getKey(),
                'atasan_id' => $atasan?->getKey(),
                'nomor_induk' => $dataKaryawan['nomor_induk'],
                'nama' => $dataKaryawan['nama'],
                'tanggal_masuk' => '2025-01-01',
                'jatah_cuti' => 12,
                'aktif' => true,
            ],
        );

        SaldoCuti::query()->firstOrCreate(
            [
                'pegawai_id' => $pegawai->getKey(),
                'tahun' => now()->year,
            ],
            [
                'jatah_awal' => 12,
                'saldo_tersedia' => 12,
                'catatan' => 'Saldo awal data dummy.',
            ],
        );

        return $pegawai;
    }
}
