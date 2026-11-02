<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Enums\StatusPengajuanCuti;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AdminHrRekapTrenBulananTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo('2026-10-15 08:00:00');
    }

    public function test_tren_bulanan_mengisi_bulan_tanpa_data_dan_membandingkan_status(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);
        PengajuanCuti::factory()->disetujui()->for($staff)->count(2)->sequence(['tanggal_cuti' => '2026-01-10'], ['tanggal_cuti' => '2026-01-11'])->create();
        PengajuanCuti::factory()->ditolak()->for($staff)->create(['tanggal_cuti' => '2026-03-05']);

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.rekap.tren_bulanan', [
                'filter' => 1,
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-03-31',
                'status' => [StatusPengajuanCuti::Disetujui->value, StatusPengajuanCuti::Ditolak->value],
            ]))
            ->assertOk()
            ->assertViewHas('rekap', function (Collection $rekap): bool {
                return $rekap->pluck('total')->all() === [2, 0, 1]
                    && $rekap[0]['jumlah_per_status'][StatusPengajuanCuti::Disetujui->value] === 2
                    && $rekap[2]['jumlah_per_status'][StatusPengajuanCuti::Ditolak->value] === 1;
            })
            ->assertViewHas('ringkasan', fn (array $ringkasan): bool => $ringkasan['jumlah_bulan'] === 3 && $ringkasan['jumlah_cuti'] === 3 && $ringkasan['jumlah_tersibuk'] === 2);
    }

    public function test_filter_tren_wajib_memilih_minimal_satu_status(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);

        $this->actingAs($adminHr->pengguna)
            ->from(route('admin_hr.rekap.tren_bulanan'))
            ->get(route('admin_hr.rekap.tren_bulanan', ['filter' => 1, 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-03-31']))
            ->assertRedirect(route('admin_hr.rekap.tren_bulanan'))
            ->assertSessionHasErrors(['status' => 'Pilih minimal satu status pengajuan.']);
    }

    public function test_admin_hr_dapat_mengunduh_tren_bulanan_dengan_status_terpilih(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);
        PengajuanCuti::factory()->disetujui()->for($staff)->create(['tanggal_cuti' => '2026-01-10']);

        $response = $this->actingAs($adminHr->pengguna)->get(route('admin_hr.rekap.tren_bulanan', [
            'filter' => 1,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-02-28',
            'status' => [StatusPengajuanCuti::Disetujui->value],
            'format' => 'excel',
        ]));

        $response->assertDownload('rekap-tren-bulanan-2026-01-01-sampai-2026-02-28.xlsx');
        $konten = $response->streamedContent();
        $this->assertStringStartsWith('PK', $konten);
        $this->assertStringContainsString('Disetujui', $konten);
        $this->assertStringContainsString('Jan 2026', $konten);
        $this->assertStringContainsString('Feb 2026', $konten);
    }

    public function test_pengguna_non_admin_hr_dilarang_membuka_tren_bulanan(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $karyawan = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);

        $this->actingAs($karyawan->pengguna)->get(route('admin_hr.rekap.tren_bulanan'))->assertForbidden();
    }

    private function buatPegawai(PeranPengguna $peran, BagianOrganisasi $bagian): Pegawai
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);

        return Pegawai::factory()->for($pengguna, 'pengguna')->for($bagian, 'bagianOrganisasi')->create();
    }
}
