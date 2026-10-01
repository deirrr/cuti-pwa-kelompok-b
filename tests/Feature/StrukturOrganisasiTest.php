<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StrukturOrganisasiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_staff_dapat_dihubungkan_dengan_satu_atasan_langsung(): void
    {
        $penggunaAtasan = Pengguna::factory()->atasan()->create();
        $atasan = Pegawai::factory()->for($penggunaAtasan, 'pengguna')->create();
        $staff = Pegawai::factory()->denganAtasan($atasan)->create();

        $this->assertTrue($staff->atasan->is($atasan));
        $this->assertTrue($atasan->bawahan->contains($staff));
    }

    public function test_pegawai_tidak_dapat_menjadi_atasan_bagi_dirinya_sendiri(): void
    {
        $pegawai = Pegawai::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Pegawai tidak dapat menjadi atasan bagi dirinya sendiri.');

        $pegawai->update(['atasan_id' => $pegawai->getKey()]);
    }

    public function test_satu_pengguna_memiliki_satu_peran_aktif(): void
    {
        $pengguna = Pengguna::factory()->create(['peran' => PeranPengguna::Karyawan]);
        $peranStaff = Peran::query()->where('kode', PeranPengguna::Karyawan->value)->firstOrFail();
        $peranAtasan = Peran::query()->where('kode', PeranPengguna::Atasan->value)->firstOrFail();

        $pengguna->peranSistem()->sync([$peranStaff->getKey()]);
        $pengguna->peranSistem()->sync([$peranAtasan->getKey()]);

        $this->assertCount(1, $pengguna->peranSistem);
        $this->assertTrue($pengguna->peranSistem->first()->is($peranAtasan));
    }
}
