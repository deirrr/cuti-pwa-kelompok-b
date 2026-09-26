<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DataAwalPengembanganTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_menyediakan_data_awal_pengembangan_dan_dapat_dijalankan_ulang(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('departemen', 2);
        $this->assertDatabaseCount('unit_bisnis', 6);
        $this->assertDatabaseCount('peran', 3);
        $this->assertDatabaseCount('pengguna_peran', 7);
        $this->assertDatabaseCount('pengguna', 4);
        $this->assertDatabaseCount('pegawai', 4);
        $this->assertDatabaseCount('jenis_cuti', 1);
        $this->assertDatabaseCount('saldo_cuti', 4);
        $this->assertDatabaseHas('pengguna', [
            'email' => 'admin.hr@example.test',
            'aktif' => true,
        ]);
    }
}
