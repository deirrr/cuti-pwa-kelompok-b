<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $this->call([
            UnitBisnisSeeder::class,
            PeranSeeder::class,
            DepartemenSeeder::class,
            PenggunaSeeder::class,
            PegawaiSeeder::class,
            JenisCutiSeeder::class,
            SaldoCutiSeeder::class,
        ]);
    }
}
