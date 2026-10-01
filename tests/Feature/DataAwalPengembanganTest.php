<?php

namespace Tests\Feature;

use App\Models\BagianOrganisasi;
use App\Models\HariLibur;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DataAwalPengembanganTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_data_awal_hanya_memuat_bagian_klinik_utama_medika_antapani(): void
    {
        $this->seed();

        $this->assertDatabaseHas('bagian_organisasi', ['kode' => 'PENDAFTARAN']);
        $this->assertDatabaseHas('bagian_organisasi', ['kode' => 'INSTALASI-FARMASI']);
        $this->assertSame(20, BagianOrganisasi::query()->count());
        $this->assertSame(17, HariLibur::query()->whereYear('tanggal', 2026)->count());
        $this->assertDatabaseHas('hari_libur', [
            'tanggal' => '2026-12-25 00:00:00',
            'nama' => 'Kelahiran Yesus Kristus',
            'jenis' => 'nasional',
            'aktif' => true,
        ]);
        $this->assertFalse(Schema::hasTable('jenis_cuti'));
    }
}
