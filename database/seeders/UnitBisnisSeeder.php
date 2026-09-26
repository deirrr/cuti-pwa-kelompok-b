<?php

namespace Database\Seeders;

use App\Enums\KategoriUnitBisnis;
use App\Models\UnitBisnis;
use Illuminate\Database\Seeder;

class UnitBisnisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitBisnis = [
            ['kode' => 'HO', 'nama' => 'Head Office', 'kategori' => KategoriUnitBisnis::HeadOffice],
            ['kode' => 'KUMA', 'nama' => 'Klinik Utama Medika Antapani', 'kategori' => KategoriUnitBisnis::Operasional],
            ['kode' => 'KPMA', 'nama' => 'Klinik Pratama Medika Antapani', 'kategori' => KategoriUnitBisnis::Operasional],
            ['kode' => 'APOTEK', 'nama' => 'Apotek Medika Antapani', 'kategori' => KategoriUnitBisnis::Operasional],
            ['kode' => 'PMB', 'nama' => 'Praktek Mandiri Bidan', 'kategori' => KategoriUnitBisnis::Operasional],
            ['kode' => 'MEDLAB', 'nama' => 'Medika Laboratorium', 'kategori' => KategoriUnitBisnis::Operasional],
        ];

        foreach ($unitBisnis as $dataUnit) {
            UnitBisnis::query()->updateOrCreate(
                ['kode' => $dataUnit['kode']],
                [...$dataUnit, 'aktif' => true],
            );
        }
    }
}
