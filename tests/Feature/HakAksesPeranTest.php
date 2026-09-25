<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HakAksesPeranTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * @return array<string, array{PeranPengguna, string}>
     */
    public static function tujuanDashboard(): array
    {
        return [
            'karyawan' => [PeranPengguna::Karyawan, 'dashboard.karyawan'],
            'atasan' => [PeranPengguna::Atasan, 'dashboard.atasan'],
            'admin HR' => [PeranPengguna::AdminHr, 'dashboard.admin_hr'],
        ];
    }

    #[DataProvider('tujuanDashboard')]
    public function test_pengguna_diarahkan_ke_dashboard_sesuai_perannya(
        PeranPengguna $peran,
        string $namaRute,
    ): void {
        $pegawai = $this->buatPegawai($peran);

        $this->actingAs($pegawai->pengguna)
            ->get(route('dashboard'))
            ->assertRedirect(route($namaRute));
    }

    public function test_semua_peran_dapat_membuka_dashboard_pengajuan_pribadi(): void
    {
        foreach (PeranPengguna::cases() as $peran) {
            $pegawai = $this->buatPegawai($peran);

            $this->actingAs($pegawai->pengguna)
                ->get(route('dashboard.karyawan'))
                ->assertOk()
                ->assertSee('Dashboard Karyawan');
        }
    }

    public function test_hanya_atasan_dapat_membuka_area_atasan(): void
    {
        $atasan = $this->buatPegawai(PeranPengguna::Atasan);
        $karyawan = $this->buatPegawai(PeranPengguna::Karyawan);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);

        $this->actingAs($atasan->pengguna)
            ->get(route('dashboard.atasan'))
            ->assertOk();
        $this->actingAs($karyawan->pengguna)
            ->get(route('dashboard.atasan'))
            ->assertForbidden();
        $this->actingAs($adminHr->pengguna)
            ->get(route('dashboard.atasan'))
            ->assertForbidden();
    }

    public function test_hanya_admin_hr_dapat_membuka_area_admin_hr(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);
        $karyawan = $this->buatPegawai(PeranPengguna::Karyawan);
        $atasan = $this->buatPegawai(PeranPengguna::Atasan);

        $this->actingAs($adminHr->pengguna)
            ->get(route('dashboard.admin_hr'))
            ->assertOk();
        $this->actingAs($karyawan->pengguna)
            ->get(route('dashboard.admin_hr'))
            ->assertForbidden();
        $this->actingAs($atasan->pengguna)
            ->get(route('dashboard.admin_hr'))
            ->assertForbidden();
    }

    private function buatPegawai(PeranPengguna $peran): Pegawai
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);

        return Pegawai::factory()
            ->for($pengguna, 'pengguna')
            ->create();
    }
}
