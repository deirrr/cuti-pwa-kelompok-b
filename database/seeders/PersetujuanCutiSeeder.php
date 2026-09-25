<?php

namespace Database\Seeders;

use App\Models\PersetujuanCuti;
use Illuminate\Database\Seeder;

class PersetujuanCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PersetujuanCuti::factory()->count(3)->create();
    }
}
