<?php

namespace Database\Seeders;

use App\Models\TanggalPengajuanCuti;
use Illuminate\Database\Seeder;

class TanggalPengajuanCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TanggalPengajuanCuti::factory()->count(3)->create();
    }
}
