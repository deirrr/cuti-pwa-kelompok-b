<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\UnitBisnis;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headOffice = UnitBisnis::query()->where('kode', 'HO')->firstOrFail();
        $klinikUtama = UnitBisnis::query()->where('kode', 'KUMA')->firstOrFail();

        Departemen::query()->updateOrCreate(
            ['kode' => 'SDM'],
            [
                'unit_bisnis_id' => $headOffice->getKey(),
                'nama' => 'Sumber Daya Manusia',
                'aktif' => true,
            ],
        );

        Departemen::query()->updateOrCreate(
            ['kode' => 'OPS'],
            [
                'unit_bisnis_id' => $klinikUtama->getKey(),
                'nama' => 'Operasional',
                'aktif' => true,
            ],
        );
    }
}
