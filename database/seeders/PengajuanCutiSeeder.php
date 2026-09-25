<?php

namespace Database\Seeders;

use App\Models\PengajuanCuti;
use Illuminate\Database\Seeder;

class PengajuanCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PengajuanCuti::factory()->count(3)->create();
    }
}
