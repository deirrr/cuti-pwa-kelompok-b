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

class AdminHrRekapStatusCutiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo('2026-10-15 08:00:00');
    }

    public function test_admin_hr_dapat_memilih_beberapa_status_dalam_periode(): void
    {
        $bagian = BagianOrganisasi::factory()->create(['nama' => 'Keperawatan']);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, ['nama' => 'Siti Aminah']);
        PengajuanCuti::factory()->disetujui()->for($staff)->create(['nomor_pengajuan' => 'CUTI-SETUJU', 'tanggal_cuti' => '2026-10-05']);
        PengajuanCuti::factory()->ditolak()->for($staff)->create(['nomor_pengajuan' => 'CUTI-TOLAK', 'tanggal_cuti' => '2026-10-06']);
        PengajuanCuti::factory()->menungguHr()->for($staff)->create(['nomor_pengajuan' => 'CUTI-TUNGGU', 'tanggal_cuti' => '2026-10-07']);
        PengajuanCuti::factory()->disetujui()->for($staff)->create(['nomor_pengajuan' => 'CUTI-LUAR', 'tanggal_cuti' => '2026-09-30']);

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.rekap.status_cuti', [
                'filter' => 1,
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2026-10-31',
                'status' => [StatusPengajuanCuti::Disetujui->value, StatusPengajuanCuti::Ditolak->value],
            ]))
            ->assertOk()
            ->assertViewHas('pengajuan', fn (Collection $items): bool => $items->pluck('nomor_pengajuan')->sort()->values()->all() === ['CUTI-SETUJU', 'CUTI-TOLAK'])
            ->assertViewHas('ringkasanStatus', fn (Collection $ringkasan): bool => $ringkasan[StatusPengajuanCuti::Disetujui->value] === 1 && $ringkasan[StatusPengajuanCuti::Ditolak->value] === 1)
            ->assertSee('Siti Aminah')
            ->assertDontSee('CUTI-TUNGGU')
            ->assertDontSee('CUTI-LUAR');
    }

    public function test_filter_status_wajib_memilih_minimal_satu_status(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);

        $this->actingAs($adminHr->pengguna)
            ->from(route('admin_hr.rekap.status_cuti'))
            ->get(route('admin_hr.rekap.status_cuti', ['filter' => 1, 'tanggal_mulai' => '2026-10-01', 'tanggal_selesai' => '2026-10-31']))
            ->assertRedirect(route('admin_hr.rekap.status_cuti'))
            ->assertSessionHasErrors(['status' => 'Pilih minimal satu status pengajuan.']);
    }

    public function test_admin_hr_dapat_mengunduh_rekap_status_sebagai_xlsx_dan_teks_bukan_formula(): void
    {
        $bagian = BagianOrganisasi::factory()->create(['nama' => 'Keperawatan']);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, ['nomor_induk' => 'PGW-002', 'nama' => '=2+3']);
        PengajuanCuti::factory()->disetujui()->for($staff)->create(['nomor_pengajuan' => 'CUTI-001', 'tanggal_cuti' => '2026-10-05']);

        $response = $this->actingAs($adminHr->pengguna)->get(route('admin_hr.rekap.status_cuti', [
            'filter' => 1,
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-31',
            'status' => [StatusPengajuanCuti::Disetujui->value],
            'format' => 'excel',
        ]));

        $response->assertDownload('rekap-status-cuti-2026-10-01-sampai-2026-10-31.xlsx');
        $konten = $response->streamedContent();
        $this->assertStringStartsWith('PK', $konten);
        $this->assertStringContainsString('<t xml:space="preserve">=2+3</t>', $konten);
        $this->assertStringNotContainsString('<f>=2+3</f>', $konten);
    }

    public function test_pengguna_non_admin_hr_dilarang_membuka_rekap_status(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $karyawan = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);

        $this->actingAs($karyawan->pengguna)->get(route('admin_hr.rekap.status_cuti'))->assertForbidden();
    }

    /** @param array<string, mixed> $atribut */
    private function buatPegawai(PeranPengguna $peran, BagianOrganisasi $bagian, array $atribut = []): Pegawai
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);

        return Pegawai::factory()->for($pengguna, 'pengguna')->for($bagian, 'bagianOrganisasi')->create($atribut);
    }
}
