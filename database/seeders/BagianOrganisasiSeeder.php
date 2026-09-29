<?php

namespace Database\Seeders;

use App\Models\BagianOrganisasi;
use Illuminate\Database\Seeder;

class BagianOrganisasiSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['kode' => 'MANAJEMEN', 'nama' => 'Manajemen'],
            ['kode' => 'SDM', 'nama' => 'Sumber Daya Manusia'],
            ['kode' => 'PENDAFTARAN', 'nama' => 'Pendaftaran'],
            ['kode' => 'SATPAM', 'nama' => 'Satpam'],
            ['kode' => 'UMUM', 'nama' => 'Umum'],
            ['kode' => 'CLEANING-SERVICE', 'nama' => 'Cleaning Service'],
            ['kode' => 'PERAWAT-GIGI', 'nama' => 'Perawat Gigi'],
            ['kode' => 'ADMIN-JKN', 'nama' => 'Admin JKN'],
            ['kode' => 'PERAWAT-UMUM', 'nama' => 'Perawat Umum'],
            ['kode' => 'INSTALASI-FARMASI', 'nama' => 'Instalasi Farmasi'],
            ['kode' => 'AMBULATUR', 'nama' => 'Ambulatur'],
            ['kode' => 'KASIR', 'nama' => 'Kasir'],
            ['kode' => 'ADMIN', 'nama' => 'Admin'],
            ['kode' => 'RADIOGRAFER', 'nama' => 'Radiografer'],
            ['kode' => 'PELAYANAN', 'nama' => 'Pelayanan'],
            ['kode' => 'DOKTER', 'nama' => 'Dokter'],
            ['kode' => 'ASISTEN-OBGYN', 'nama' => 'Asisten Obgyn'],
            ['kode' => 'KESMAS', 'nama' => 'Kesehatan Masyarakat'],
            ['kode' => 'TVF', 'nama' => 'Tenaga Vokasi Farmasi'],
            ['kode' => 'APOTEKER', 'nama' => 'Apoteker'],
        ] as $bagian) {
            BagianOrganisasi::query()->updateOrCreate(
                ['kode' => $bagian['kode']],
                ['nama' => $bagian['nama'], 'aktif' => true],
            );
        }
    }
}
