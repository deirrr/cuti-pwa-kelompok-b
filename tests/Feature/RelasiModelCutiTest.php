<?php

namespace Tests\Feature;

use App\Enums\KeputusanPersetujuan;
use App\Enums\StatusPengajuanCuti;
use App\Enums\TahapPersetujuan;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use App\Models\PersetujuanCuti;
use App\Models\SaldoCuti;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RelasiModelCutiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_relasi_organisasi_dan_saldo_cuti_dapat_diakses(): void
    {
        $bagianOrganisasi = BagianOrganisasi::factory()->create();
        $penggunaAtasan = Pengguna::factory()->atasan()->create();
        $atasan = Pegawai::factory()
            ->for($bagianOrganisasi, 'bagianOrganisasi')
            ->for($penggunaAtasan, 'pengguna')
            ->create();
        $penggunaKaryawan = Pengguna::factory()->create();
        $pegawai = Pegawai::factory()
            ->for($bagianOrganisasi, 'bagianOrganisasi')
            ->for($penggunaKaryawan, 'pengguna')
            ->denganAtasan($atasan)
            ->create();
        $saldoCuti = SaldoCuti::factory()
            ->for($pegawai, 'pegawai')
            ->create();

        $this->assertTrue($pegawai->pengguna->is($penggunaKaryawan));
        $this->assertTrue($pegawai->bagianOrganisasi->is($bagianOrganisasi));
        $this->assertTrue($pegawai->atasan->is($atasan));
        $this->assertTrue($atasan->bawahan->contains($pegawai));
        $this->assertTrue($pegawai->saldoCuti->contains($saldoCuti));
    }

    public function test_pengajuan_memuat_tanggal_dan_riwayat_persetujuan(): void
    {
        $pengajuan = PengajuanCuti::factory()->menungguHr()->create([
            'tanggal_cuti' => '2026-10-05',
        ]);
        $pemberiKeputusan = Pengguna::factory()->atasan()->create();
        $persetujuan = PersetujuanCuti::factory()
            ->for($pengajuan, 'pengajuanCuti')
            ->for($pemberiKeputusan, 'pemberiKeputusan')
            ->create();

        $pengajuan->refresh();

        $this->assertSame(StatusPengajuanCuti::MenungguHr, $pengajuan->status);
        $this->assertSame('2026-10-05', $pengajuan->tanggal_cuti->toDateString());
        $this->assertTrue($pengajuan->persetujuan->contains($persetujuan));
        $this->assertSame(TahapPersetujuan::Atasan, $persetujuan->tahap);
        $this->assertSame(KeputusanPersetujuan::Disetujui, $persetujuan->keputusan);
        $this->assertTrue($persetujuan->pemberiKeputusan->is($pemberiKeputusan));
    }
}
