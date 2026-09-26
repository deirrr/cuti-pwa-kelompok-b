<?php

namespace Database\Seeders;

use App\Enums\PeranPengguna;
use App\Models\Peran;
use Illuminate\Database\Seeder;

class PeranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (PeranPengguna::cases() as $peran) {
            Peran::query()->updateOrCreate(
                ['kode' => $peran->value],
                ['nama' => $peran->label(), 'aktif' => true],
            );
        }
    }
}
