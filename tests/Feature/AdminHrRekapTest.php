<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminHrRekapTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_hr_dapat_membuka_pusat_rekap_dengan_empat_jenis_laporan(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);

        $this->actingAs($adminHr)
            ->get(route('admin_hr.rekap.index'))
            ->assertOk()
            ->assertViewIs('admin-hr.rekap.index')
            ->assertSee('href="'.route('admin_hr.rekap.index').'"', false)
            ->assertSee('href="'.route('admin_hr.rekap.sisa_cuti').'"', false)
            ->assertSee('href="'.route('admin_hr.rekap.status_cuti').'"', false)
            ->assertSee('href="'.route('admin_hr.rekap.penggunaan_bagian').'"', false)
            ->assertSee('href="'.route('admin_hr.rekap.tren_bulanan').'"', false)
            ->assertSeeInOrder([
                'Rekap Sisa Cuti',
                'Rekap Cuti Berdasarkan Status',
                'Penggunaan Cuti per Bagian',
                'Tren Cuti Bulanan',
            ]);
    }

    public function test_pengguna_tanpa_peran_admin_hr_dilarang_membuka_pusat_rekap(): void
    {
        $karyawan = $this->buatPengguna(PeranPengguna::Karyawan);

        $this->actingAs($karyawan)
            ->get(route('admin_hr.rekap.index'))
            ->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_halaman_masuk_saat_membuka_pusat_rekap(): void
    {
        $this->get(route('admin_hr.rekap.index'))
            ->assertRedirect(route('login'));
    }

    private function buatPengguna(PeranPengguna $peran): Pengguna
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);
        Pegawai::factory()->for($pengguna, 'pengguna')->create();

        return $pengguna;
    }
}
