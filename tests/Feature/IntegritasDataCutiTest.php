<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\PersetujuanCuti;
use App\Models\SaldoCuti;
use App\Models\TanggalPengajuanCuti;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class IntegritasDataCutiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_saldo_cuti_hanya_satu_per_pegawai_jenis_dan_tahun(): void
    {
        $saldo = SaldoCuti::factory()->create();

        $this->expectException(QueryException::class);

        SaldoCuti::factory()->create([
            'pegawai_id' => $saldo->pegawai_id,
            'jenis_cuti_id' => $saldo->jenis_cuti_id,
            'tahun' => $saldo->tahun,
        ]);
    }

    public function test_tanggal_yang_sama_tidak_dapat_diulang_dalam_satu_pengajuan(): void
    {
        $tanggal = TanggalPengajuanCuti::factory()->create([
            'tanggal' => '2026-10-05',
        ]);

        $this->expectException(QueryException::class);

        TanggalPengajuanCuti::factory()->create([
            'pengajuan_cuti_id' => $tanggal->pengajuan_cuti_id,
            'tanggal' => $tanggal->tanggal,
        ]);
    }

    public function test_satu_tahap_persetujuan_hanya_dapat_dicatat_sekali(): void
    {
        $persetujuan = PersetujuanCuti::factory()->create();

        $this->expectException(QueryException::class);

        PersetujuanCuti::factory()->create([
            'pengajuan_cuti_id' => $persetujuan->pengajuan_cuti_id,
            'tahap' => $persetujuan->tahap,
        ]);
    }

    public function test_saldo_cuti_tidak_dapat_bernilai_negatif(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Saldo cuti tidak boleh bernilai negatif.');

        SaldoCuti::factory()->create(['saldo_tersedia' => -1]);
    }

    public function test_pegawai_tidak_dapat_menjadi_atasan_dirinya_sendiri(): void
    {
        $pegawai = Pegawai::factory()->create();
        $pegawai->atasan_id = $pegawai->getKey();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Pegawai tidak dapat menjadi atasan bagi dirinya sendiri.');

        $pegawai->save();
    }

    public function test_pengajuan_memiliki_nomor_yang_unik(): void
    {
        $pengajuan = PengajuanCuti::factory()->create();

        $this->expectException(QueryException::class);

        PengajuanCuti::factory()->create([
            'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
        ]);
    }
}
