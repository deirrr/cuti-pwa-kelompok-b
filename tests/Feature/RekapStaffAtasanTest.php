<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuanCuti;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use App\Models\SaldoCuti;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RekapStaffAtasanTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo('2026-11-02 08:00:00');
    }

    public function test_atasan_melihat_menu_rekap_staff_di_navigasi_dan_dashboard(): void
    {
        $atasan = $this->buatAtasan();

        $this->actingAs($atasan->pengguna)
            ->get(route('dashboard.atasan'))
            ->assertOk()
            ->assertSee(route('rekap_staff.index'))
            ->assertSee('Rekap Staff');
    }

    public function test_rekap_hanya_memuat_pengajuan_staff_langsung_milik_atasan(): void
    {
        $atasan = $this->buatAtasan();
        $atasanLain = $this->buatAtasan();
        $staffSendiri = $this->buatStaff($atasan, ['nama' => 'Staff Sendiri']);
        $staffLain = $this->buatStaff($atasanLain, ['nama' => 'Staff Atasan Lain']);
        PengajuanCuti::factory()->disetujui()->for($staffSendiri)->create([
            'nomor_pengajuan' => 'CUTI-STAFF-SENDIRI',
            'tanggal_cuti' => '2026-10-10',
        ]);
        PengajuanCuti::factory()->ditolak()->for($staffSendiri)->create([
            'nomor_pengajuan' => 'CUTI-STATUS-LAIN',
            'tanggal_cuti' => '2026-10-11',
        ]);
        PengajuanCuti::factory()->disetujui()->for($staffLain)->create([
            'nomor_pengajuan' => 'CUTI-STAFF-LAIN',
            'tanggal_cuti' => '2026-10-12',
        ]);

        $this->actingAs($atasan->pengguna)
            ->get(route('rekap_staff.index', [
                'filter' => 1,
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2026-10-31',
                'status' => [StatusPengajuanCuti::Disetujui->value],
            ]))
            ->assertOk()
            ->assertViewHas('pengajuan', fn (Collection $items): bool => $items->pluck('nomor_pengajuan')->all() === ['CUTI-STAFF-SENDIRI'])
            ->assertSee('Staff Sendiri')
            ->assertDontSee('CUTI-STATUS-LAIN')
            ->assertDontSee('CUTI-STAFF-LAIN')
            ->assertSee('Export Excel')
            ->assertSee('Print');
    }

    public function test_karyawan_tidak_dapat_membuka_rekap_staff(): void
    {
        $karyawan = Pegawai::factory()
            ->for(Pengguna::factory(), 'pengguna')
            ->for(BagianOrganisasi::factory(), 'bagianOrganisasi')
            ->create();

        $this->actingAs($karyawan->pengguna)
            ->get(route('rekap_staff.index'))
            ->assertForbidden();
    }

    public function test_atasan_dapat_melihat_sisa_cuti_staff_langsung(): void
    {
        $atasan = $this->buatAtasan();
        $atasanLain = $this->buatAtasan();
        $staffSendiri = $this->buatStaff($atasan, ['nama' => 'Staff Saldo Sendiri']);
        $staffLain = $this->buatStaff($atasanLain, ['nama' => 'Staff Saldo Lain']);
        SaldoCuti::factory()->for($staffSendiri)->create([
            'tahun' => 2026,
            'jatah_awal' => 12,
            'saldo_tersedia' => 8,
        ]);
        SaldoCuti::factory()->for($staffLain)->create([
            'tahun' => 2026,
            'jatah_awal' => 12,
            'saldo_tersedia' => 4,
        ]);
        PengajuanCuti::factory()->disetujui()->for($staffSendiri)->create(['tanggal_cuti' => '2026-10-10']);
        PengajuanCuti::factory()->menungguHr()->for($staffSendiri)->create(['tanggal_cuti' => '2026-10-11']);

        $this->actingAs($atasan->pengguna)
            ->get(route('rekap_staff.index', [
                'filter' => 1,
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2026-10-31',
                'status' => [StatusPengajuanCuti::Disetujui->value, StatusPengajuanCuti::MenungguHr->value],
            ]))
            ->assertOk()
            ->assertViewHas('rekapSaldo', function (Collection $items): bool {
                $saldo = $items->sole();

                return $saldo['nama'] === 'Staff Saldo Sendiri'
                    && $saldo['jatah_awal'] === 12
                    && $saldo['cuti_disetujui_periode'] === 1
                    && $saldo['cuti_menunggu_periode'] === 1
                    && $saldo['saldo_tersedia'] === 8
                    && $saldo['saldo_efektif'] === 7;
            })
            ->assertSee('Sisa cuti Staff tahun 2026')
            ->assertSee('Staff Saldo Sendiri')
            ->assertDontSee('Staff Saldo Lain');
    }

    public function test_atasan_dapat_mengunduh_rekap_staff_sesuai_cakupan(): void
    {
        $atasan = $this->buatAtasan();
        $atasanLain = $this->buatAtasan();
        $staffSendiri = $this->buatStaff($atasan, ['nomor_induk' => 'PGW-SENDIRI']);
        $staffLain = $this->buatStaff($atasanLain, ['nomor_induk' => 'PGW-LAIN']);
        $staffTanpaPengajuan = $this->buatStaff($atasan, ['nomor_induk' => 'PGW-TANPA-PENGAJUAN']);
        SaldoCuti::factory()->for($staffSendiri)->create(['tahun' => 2026, 'jatah_awal' => 12, 'saldo_tersedia' => 8]);
        SaldoCuti::factory()->for($staffTanpaPengajuan)->create(['tahun' => 2026, 'jatah_awal' => 12, 'saldo_tersedia' => 12]);
        PengajuanCuti::factory()->disetujui()->for($staffSendiri)->create(['tanggal_cuti' => '2026-10-10']);
        PengajuanCuti::factory()->disetujui()->for($staffLain)->create(['tanggal_cuti' => '2026-10-11']);

        $response = $this->actingAs($atasan->pengguna)
            ->get(route('rekap_staff.index', [
                'filter' => 1,
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2026-10-31',
                'status' => [StatusPengajuanCuti::Disetujui->value],
                'format' => 'excel',
            ]));

        $response->assertDownload('rekap-staff-2026-10-01-sampai-2026-10-31.xlsx');
        $konten = $response->streamedContent();
        $this->assertStringContainsString('PGW-SENDIRI', $konten);
        $this->assertStringContainsString('PGW-TANPA-PENGAJUAN', $konten);
        $this->assertStringNotContainsString('PGW-LAIN', $konten);
        $this->assertSame(2, substr_count($konten, '<sheet name="'));
        $this->assertStringContainsString('name="Detail Pengajuan"', $konten);
        $this->assertStringContainsString('name="Sisa Cuti Staff"', $konten);
        $this->assertStringContainsString('xl/worksheets/sheet2.xml', $konten);
        $this->assertStringContainsString('Sisa Saat Ini', $konten);
    }

    public function test_filter_rekap_staff_wajib_memilih_minimal_satu_status(): void
    {
        $atasan = $this->buatAtasan();

        $this->actingAs($atasan->pengguna)
            ->from(route('rekap_staff.index'))
            ->get(route('rekap_staff.index', [
                'filter' => 1,
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2026-10-31',
            ]))
            ->assertRedirect(route('rekap_staff.index'))
            ->assertSessionHasErrors(['status' => 'Pilih minimal satu status pengajuan.']);
    }

    public function test_periode_rekap_staff_harus_dalam_tahun_yang_sama(): void
    {
        $atasan = $this->buatAtasan();

        $this->actingAs($atasan->pengguna)
            ->from(route('rekap_staff.index'))
            ->get(route('rekap_staff.index', [
                'filter' => 1,
                'tanggal_mulai' => '2025-12-01',
                'tanggal_selesai' => '2026-01-31',
                'status' => [StatusPengajuanCuti::Disetujui->value],
            ]))
            ->assertRedirect(route('rekap_staff.index'))
            ->assertSessionHasErrors(['tanggal_selesai' => 'Periode laporan harus berada dalam tahun yang sama.']);
    }

    /** @param array<string, mixed> $atribut */
    private function buatAtasan(array $atribut = []): Pegawai
    {
        return Pegawai::factory()
            ->for(Pengguna::factory()->atasan(), 'pengguna')
            ->for(BagianOrganisasi::factory(), 'bagianOrganisasi')
            ->create($atribut);
    }

    /** @param array<string, mixed> $atribut */
    private function buatStaff(Pegawai $atasan, array $atribut = []): Pegawai
    {
        return Pegawai::factory()
            ->denganAtasan($atasan)
            ->for(Pengguna::factory(), 'pengguna')
            ->create($atribut);
    }
}
