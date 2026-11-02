<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use App\Models\SaldoCuti;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AdminHrRekapSisaCutiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->travelTo('2026-10-15 08:00:00');
    }

    public function test_admin_hr_melihat_saldo_dan_penggunaan_cuti_pada_periode_terpilih(): void
    {
        $bagian = BagianOrganisasi::factory()->create(['nama' => 'Keperawatan']);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, [
            'nomor_induk' => 'PGW-001',
            'nama' => 'Siti Aminah',
        ]);
        SaldoCuti::factory()->for($staff)->create([
            'tahun' => 2026,
            'jatah_awal' => 12,
            'saldo_tersedia' => 8,
        ]);
        PengajuanCuti::factory()->disetujui()->for($staff)->create(['tanggal_cuti' => '2026-09-15']);
        PengajuanCuti::factory()->disetujui()->for($staff)->create(['tanggal_cuti' => '2026-10-05']);
        PengajuanCuti::factory()->menungguAtasan()->for($staff)->create(['tanggal_cuti' => '2026-10-10']);
        PengajuanCuti::factory()->menungguHr()->for($staff)->create(['tanggal_cuti' => '2026-11-05']);
        PengajuanCuti::factory()->ditolak()->for($staff)->create(['tanggal_cuti' => '2026-10-11']);

        $response = $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.rekap.sisa_cuti', [
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2026-10-31',
            ]));

        $response
            ->assertOk()
            ->assertViewIs('admin-hr.rekap.sisa-cuti')
            ->assertViewHas('rekap', function (Collection $rekap): bool {
                $staff = $rekap->firstWhere('nomor_induk', 'PGW-001');

                return $staff !== null
                    && $staff['jatah_awal'] === 12
                    && $staff['cuti_disetujui_periode'] === 1
                    && $staff['cuti_disetujui_tahun'] === 2
                    && $staff['cuti_menunggu_periode'] === 1
                    && $staff['saldo_tersedia'] === 8
                    && $staff['saldo_efektif'] === 7;
            })
            ->assertViewHas('ringkasan', [
                'jumlah_karyawan' => 1,
                'total_jatah_awal' => 12,
                'total_cuti_disetujui_periode' => 1,
                'total_saldo_tersedia' => 8,
            ])
            ->assertSee('Siti Aminah');
    }

    public function test_filter_bagian_status_dan_pencarian_membatasi_karyawan_yang_ditampilkan(): void
    {
        $bagianKeperawatan = BagianOrganisasi::factory()->create(['nama' => 'Keperawatan']);
        $bagianFarmasi = BagianOrganisasi::factory()->create(['nama' => 'Farmasi']);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagianKeperawatan);
        $this->buatPegawai(PeranPengguna::Karyawan, $bagianKeperawatan, [
            'nomor_induk' => 'PGW-AKTIF',
            'nama' => 'Staff Aktif',
        ]);
        $this->buatPegawai(PeranPengguna::Karyawan, $bagianKeperawatan, [
            'nomor_induk' => 'PGW-NONAKTIF',
            'nama' => 'Staff Nonaktif',
            'aktif' => false,
        ]);
        $this->buatPegawai(PeranPengguna::Karyawan, $bagianFarmasi, [
            'nomor_induk' => 'PGW-FARMASI',
            'nama' => 'Staff Farmasi',
        ]);

        $response = $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.rekap.sisa_cuti', [
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-10-15',
                'bagian_organisasi_id' => $bagianKeperawatan->getKey(),
                'status_pegawai' => 'aktif',
                'cari' => 'PGW-AKTIF',
            ]));

        $response
            ->assertOk()
            ->assertViewHas('rekap', fn (Collection $rekap): bool => $rekap->pluck('nomor_induk')->all() === ['PGW-AKTIF'])
            ->assertSee('Staff Aktif')
            ->assertDontSee('Staff Nonaktif')
            ->assertDontSee('Staff Farmasi');
    }

    public function test_pegawai_tanpa_saldo_tahunan_ditandai_belum_diterbitkan(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $this->buatPegawai(PeranPengguna::Karyawan, $bagian, [
            'nomor_induk' => 'PGW-TANPA-SALDO',
            'nama' => 'Belum Memiliki Saldo',
        ]);

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.rekap.sisa_cuti'))
            ->assertOk()
            ->assertSee('Saldo 2026 belum diterbitkan');
    }

    public function test_tanggal_selesai_tidak_boleh_sebelum_tanggal_mulai(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);

        $this->actingAs($adminHr->pengguna)
            ->from(route('admin_hr.rekap.sisa_cuti'))
            ->get(route('admin_hr.rekap.sisa_cuti', [
                'tanggal_mulai' => '2026-12-01',
                'tanggal_selesai' => '2026-11-01',
            ]))
            ->assertRedirect(route('admin_hr.rekap.sisa_cuti'))
            ->assertSessionHasErrors([
                'tanggal_selesai' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            ]);
    }

    public function test_periode_laporan_harus_berada_dalam_tahun_yang_sama(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);

        $this->actingAs($adminHr->pengguna)
            ->from(route('admin_hr.rekap.sisa_cuti'))
            ->get(route('admin_hr.rekap.sisa_cuti', [
                'tanggal_mulai' => '2026-12-01',
                'tanggal_selesai' => '2027-01-31',
            ]))
            ->assertRedirect(route('admin_hr.rekap.sisa_cuti'))
            ->assertSessionHasErrors([
                'tanggal_selesai' => 'Periode laporan harus berada dalam tahun yang sama.',
            ]);
    }

    public function test_pengguna_non_admin_hr_dilarang_membuka_rekap_sisa_cuti(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $karyawan = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);

        $this->actingAs($karyawan->pengguna)
            ->get(route('admin_hr.rekap.sisa_cuti'))
            ->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_halaman_masuk_saat_membuka_rekap_sisa_cuti(): void
    {
        $this->get(route('admin_hr.rekap.sisa_cuti'))
            ->assertRedirect(route('login'));
    }

    /** @param array<string, mixed> $atribut */
    private function buatPegawai(
        PeranPengguna $peran,
        BagianOrganisasi $bagian,
        array $atribut = [],
    ): Pegawai {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);

        return Pegawai::factory()
            ->for($pengguna, 'pengguna')
            ->for($bagian, 'bagianOrganisasi')
            ->create($atribut);
    }
}
