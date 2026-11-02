<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AdminHrRekapPenggunaanBagianTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo('2026-10-15 08:00:00');
    }

    public function test_laporan_menghitung_hari_cuti_disetujui_dan_karyawan_unik_per_bagian(): void
    {
        $keperawatan = BagianOrganisasi::factory()->create(['nama' => 'Keperawatan']);
        $farmasi = BagianOrganisasi::factory()->create(['nama' => 'Farmasi']);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $keperawatan);
        $staffSatu = $this->buatPegawai(PeranPengguna::Karyawan, $keperawatan);
        $staffDua = $this->buatPegawai(PeranPengguna::Karyawan, $keperawatan);
        $staffFarmasi = $this->buatPegawai(PeranPengguna::Karyawan, $farmasi);
        PengajuanCuti::factory()->disetujui()->for($staffSatu)->count(2)->sequence(['tanggal_cuti' => '2026-10-03'], ['tanggal_cuti' => '2026-10-04'])->create();
        PengajuanCuti::factory()->disetujui()->for($staffDua)->create(['tanggal_cuti' => '2026-10-05']);
        PengajuanCuti::factory()->menungguHr()->for($staffDua)->create(['tanggal_cuti' => '2026-10-06']);
        PengajuanCuti::factory()->disetujui()->for($staffFarmasi)->create(['tanggal_cuti' => '2026-10-07']);

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.rekap.penggunaan_bagian', ['tanggal_mulai' => '2026-10-01', 'tanggal_selesai' => '2026-10-31']))
            ->assertOk()
            ->assertViewHas('rekap', function (Collection $rekap): bool {
                $keperawatan = $rekap->firstWhere('nama', 'Keperawatan');
                $farmasi = $rekap->firstWhere('nama', 'Farmasi');

                return $keperawatan['jumlah_pegawai_cuti'] === 2
                    && $keperawatan['jumlah_hari_cuti'] === 3
                    && $keperawatan['rata_rata_hari'] === 1.5
                    && $farmasi['jumlah_hari_cuti'] === 1;
            })
            ->assertViewHas('ringkasan', ['jumlah_bagian' => 2, 'jumlah_pegawai_cuti' => 3, 'jumlah_hari_cuti' => 4, 'rata_rata_hari' => 1.3]);
    }

    public function test_filter_bagian_hanya_menampilkan_bagian_terpilih(): void
    {
        $bagianSatu = BagianOrganisasi::factory()->create(['nama' => 'Keperawatan']);
        BagianOrganisasi::factory()->create(['nama' => 'Farmasi']);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagianSatu);

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.rekap.penggunaan_bagian', ['bagian_organisasi_id' => $bagianSatu->getKey()]))
            ->assertOk()
            ->assertViewHas('rekap', fn (Collection $rekap): bool => $rekap->pluck('nama')->all() === ['Keperawatan']);
    }

    public function test_admin_hr_dapat_mengunduh_rekap_penggunaan_bagian(): void
    {
        $bagian = BagianOrganisasi::factory()->create(['nama' => 'Farmasi']);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);
        PengajuanCuti::factory()->disetujui()->for($staff)->create(['tanggal_cuti' => '2026-10-05']);

        $response = $this->actingAs($adminHr->pengguna)->get(route('admin_hr.rekap.penggunaan_bagian', [
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-31',
            'format' => 'excel',
        ]));

        $response->assertDownload('rekap-penggunaan-bagian-2026-10-01-sampai-2026-10-31.xlsx');
        $konten = $response->streamedContent();
        $this->assertStringStartsWith('PK', $konten);
        $this->assertStringContainsString('Rekap Penggunaan Cuti per Bagian', $konten);
        $this->assertStringContainsString('Farmasi', $konten);
    }

    public function test_pengguna_non_admin_hr_dilarang_membuka_rekap_per_bagian(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $karyawan = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);

        $this->actingAs($karyawan->pengguna)->get(route('admin_hr.rekap.penggunaan_bagian'))->assertForbidden();
    }

    private function buatPegawai(PeranPengguna $peran, BagianOrganisasi $bagian): Pegawai
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);

        return Pegawai::factory()->for($pengguna, 'pengguna')->for($bagian, 'bagianOrganisasi')->create();
    }
}
