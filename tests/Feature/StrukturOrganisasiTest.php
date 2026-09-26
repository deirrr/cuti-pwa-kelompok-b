<?php

namespace Tests\Feature;

use App\Enums\KategoriJabatan;
use App\Enums\KategoriUnitBisnis;
use App\Enums\PeranPengguna;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\PenugasanJabatan;
use App\Models\Peran;
use App\Models\UnitBisnis;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StrukturOrganisasiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_unit_departemen_dan_hierarki_jabatan_dapat_direlasikan(): void
    {
        $unit = UnitBisnis::factory()->create([
            'kategori' => KategoriUnitBisnis::HeadOffice,
        ]);
        $departemen = Departemen::factory()->for($unit, 'unitBisnis')->create();
        $direktur = Jabatan::factory()->for($unit, 'unitBisnis')->create([
            'departemen_id' => null,
            'kategori' => KategoriJabatan::Direktur,
        ]);
        $supervisor = Jabatan::factory()
            ->for($unit, 'unitBisnis')
            ->for($departemen, 'departemen')
            ->for($direktur, 'atasanJabatan')
            ->create(['kategori' => KategoriJabatan::Supervisor]);

        $this->assertTrue($departemen->unitBisnis->is($unit));
        $this->assertTrue($supervisor->atasanJabatan->is($direktur));
        $this->assertTrue($direktur->bawahanJabatan->contains($supervisor));
    }

    public function test_pegawai_dapat_memiliki_beberapa_jabatan_dengan_satu_penugasan_utama(): void
    {
        $pegawai = Pegawai::factory()->create();
        $jabatanUtama = Jabatan::factory()->create();
        $jabatanRangkap = Jabatan::factory()->create();

        $penugasanUtama = PenugasanJabatan::factory()
            ->for($pegawai, 'pegawai')
            ->for($jabatanUtama, 'jabatan')
            ->utama()
            ->create();
        PenugasanJabatan::factory()
            ->for($pegawai, 'pegawai')
            ->for($jabatanRangkap, 'jabatan')
            ->create();

        $pegawai->refresh();

        $this->assertCount(2, $pegawai->penugasanJabatan);
        $this->assertCount(2, $pegawai->jabatanOrganisasi);
        $this->assertTrue($penugasanUtama->utama);
    }

    public function test_pegawai_tidak_dapat_memiliki_dua_penugasan_utama_aktif(): void
    {
        $pegawai = Pegawai::factory()->create();
        PenugasanJabatan::factory()->for($pegawai, 'pegawai')->utama()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Pegawai hanya boleh memiliki satu penugasan utama yang aktif.');

        PenugasanJabatan::factory()->for($pegawai, 'pegawai')->utama()->create();
    }

    public function test_satu_pengguna_dapat_memiliki_beberapa_peran_dan_mengakses_fungsinya(): void
    {
        $pengguna = Pengguna::factory()->create(['peran' => PeranPengguna::Karyawan]);
        Pegawai::factory()->for($pengguna, 'pengguna')->create();
        $peranAtasan = Peran::query()->where('kode', PeranPengguna::Atasan->value)->firstOrFail();
        $peranAdminHr = Peran::query()->where('kode', PeranPengguna::AdminHr->value)->firstOrFail();

        $pengguna->peranSistem()->attach([$peranAtasan->getKey(), $peranAdminHr->getKey()]);

        $this->assertTrue($pengguna->memilikiPeran(PeranPengguna::Karyawan));
        $this->assertTrue($pengguna->memilikiPeran(PeranPengguna::Atasan));
        $this->assertTrue($pengguna->memilikiPeran(PeranPengguna::AdminHr));

        $this->actingAs($pengguna)
            ->get(route('dashboard.atasan'))
            ->assertOk();
        $this->actingAs($pengguna)
            ->get(route('dashboard.admin_hr'))
            ->assertOk();
        $this->actingAs($pengguna)
            ->get(route('dashboard'))
            ->assertRedirect(route('dashboard.admin_hr'));
    }
}
