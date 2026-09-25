<?php

namespace Tests\Feature;

use App\Enums\KeputusanPersetujuan;
use App\Enums\StatusPengajuanCuti;
use App\Enums\TahapPersetujuan;
use App\Models\Departemen;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use App\Models\PersetujuanCuti;
use App\Models\SaldoCuti;
use App\Models\TanggalPengajuanCuti;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RelasiModelCutiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_relasi_organisasi_dan_saldo_cuti_dapat_diakses(): void
    {
        $departemen = Departemen::factory()->create();
        $penggunaAtasan = Pengguna::factory()->atasan()->create();
        $atasan = Pegawai::factory()
            ->for($departemen, 'departemen')
            ->for($penggunaAtasan, 'pengguna')
            ->create();
        $penggunaKaryawan = Pengguna::factory()->create();
        $pegawai = Pegawai::factory()
            ->for($departemen, 'departemen')
            ->for($penggunaKaryawan, 'pengguna')
            ->denganAtasan($atasan)
            ->create();
        $jenisCuti = JenisCuti::factory()->create();
        $saldoCuti = SaldoCuti::factory()
            ->for($pegawai, 'pegawai')
            ->for($jenisCuti, 'jenisCuti')
            ->create();

        $this->assertTrue($pegawai->pengguna->is($penggunaKaryawan));
        $this->assertTrue($pegawai->departemen->is($departemen));
        $this->assertTrue($pegawai->atasan->is($atasan));
        $this->assertTrue($atasan->bawahan->contains($pegawai));
        $this->assertTrue($pegawai->saldoCuti->contains($saldoCuti));
        $this->assertTrue($saldoCuti->jenisCuti->is($jenisCuti));
    }

    public function test_pengajuan_memuat_tanggal_dan_riwayat_persetujuan(): void
    {
        $pengajuan = PengajuanCuti::factory()->menungguHr()->create([
            'jumlah_hari' => 2,
        ]);
        TanggalPengajuanCuti::factory()
            ->for($pengajuan, 'pengajuanCuti')
            ->create(['tanggal' => '2026-10-05']);
        TanggalPengajuanCuti::factory()
            ->for($pengajuan, 'pengajuanCuti')
            ->create(['tanggal' => '2026-10-06']);
        $pemberiKeputusan = Pengguna::factory()->atasan()->create();
        $persetujuan = PersetujuanCuti::factory()
            ->for($pengajuan, 'pengajuanCuti')
            ->for($pemberiKeputusan, 'pemberiKeputusan')
            ->create();

        $pengajuan->refresh();

        $this->assertSame(StatusPengajuanCuti::MenungguHr, $pengajuan->status);
        $this->assertCount(2, $pengajuan->tanggalCuti);
        $this->assertTrue($pengajuan->persetujuan->contains($persetujuan));
        $this->assertSame(TahapPersetujuan::Atasan, $persetujuan->tahap);
        $this->assertSame(KeputusanPersetujuan::Disetujui, $persetujuan->keputusan);
        $this->assertTrue($persetujuan->pemberiKeputusan->is($pemberiKeputusan));
    }
}
