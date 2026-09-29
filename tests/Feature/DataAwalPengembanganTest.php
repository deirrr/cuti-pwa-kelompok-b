<?php

namespace Tests\Feature;

use App\Models\BagianOrganisasi;
use App\Models\UnitBisnis;
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

        $this->assertDatabaseCount('bagian_organisasi', 57);

        $headOffice = UnitBisnis::query()->where('kode', 'HO')->firstOrFail();
        $klinikUtama = UnitBisnis::query()->where('kode', 'KUMA')->firstOrFail();
        $klinikPratama = UnitBisnis::query()->where('kode', 'KPMA')->firstOrFail();
        $apotek = UnitBisnis::query()->where('kode', 'APOTEK')->firstOrFail();
        $praktikMandiriBidan = UnitBisnis::query()->where('kode', 'PMB')->firstOrFail();
        $medikaLaboratorium = UnitBisnis::query()->where('kode', 'MEDLAB')->firstOrFail();

        $this->assertDatabaseHas('bagian_organisasi', [
            'unit_bisnis_id' => $klinikUtama->getKey(),
            'kode' => 'PENDAFTARAN',
            'nama' => 'Pendaftaran',
            'jenis' => 'bagian',
        ]);
        $this->assertDatabaseHas('bagian_organisasi', [
            'unit_bisnis_id' => $klinikPratama->getKey(),
            'kode' => 'PERAWAT-GIGI',
            'nama' => 'Perawat Gigi',
            'jenis' => 'bagian',
        ]);
        $this->assertDatabaseHas('bagian_organisasi', [
            'unit_bisnis_id' => $apotek->getKey(),
            'kode' => 'TVF',
            'nama' => 'Tenaga Vokasi Farmasi',
            'jenis' => 'bagian',
        ]);
        $this->assertDatabaseHas('bagian_organisasi', [
            'unit_bisnis_id' => $praktikMandiriBidan->getKey(),
            'kode' => 'PELAYANAN-KEBIDANAN',
            'nama' => 'Pelayanan Kebidanan',
            'jenis' => 'bagian',
        ]);
        $this->assertDatabaseHas('bagian_organisasi', [
            'unit_bisnis_id' => $medikaLaboratorium->getKey(),
            'kode' => 'LABORATORIUM',
            'nama' => 'Laboratorium',
            'jenis' => 'bagian',
        ]);

        $direktoratUmum = BagianOrganisasi::query()
            ->whereBelongsTo($headOffice, 'unitBisnis')
            ->where('kode', 'DIR-UMUM')
            ->firstOrFail();

        $this->assertDatabaseHas('bagian_organisasi', [
            'unit_bisnis_id' => $headOffice->getKey(),
            'induk_id' => $direktoratUmum->getKey(),
            'kode' => 'SDM',
            'jenis' => 'departemen',
        ]);
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
