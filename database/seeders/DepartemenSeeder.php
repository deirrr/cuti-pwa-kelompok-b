<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Departemen::query()->updateOrCreate(
            ['kode' => 'SDM'],
            ['nama' => 'Sumber Daya Manusia', 'aktif' => true],
        );

        Departemen::query()->updateOrCreate(
            ['kode' => 'OPS'],
            ['nama' => 'Operasional', 'aktif' => true],
        );
    }
}
