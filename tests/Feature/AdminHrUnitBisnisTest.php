<?php

namespace Tests\Feature;

use App\Enums\KategoriUnitBisnis;
use App\Enums\PeranPengguna;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\UnitBisnis;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminHrUnitBisnisTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_hr_dapat_membuka_dashboard_dan_daftar_unit_bisnis(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unit = UnitBisnis::factory()->create(['nama' => 'Klinik Contoh']);

        $this->actingAs($adminHr)
            ->get(route('admin_hr.dashboard'))
            ->assertOk()
            ->assertSee('Kelola fondasi organisasi');

        $this->actingAs($adminHr)
            ->get(route('admin_hr.unit_bisnis.index'))
            ->assertOk()
            ->assertSee($unit->nama);
    }

    public function test_pengguna_tanpa_peran_admin_hr_tidak_dapat_membuka_pengelolaan_unit(): void
    {
        $karyawan = $this->buatPengguna(PeranPengguna::Karyawan);
        $atasan = $this->buatPengguna(PeranPengguna::Atasan);

        $this->actingAs($karyawan)
            ->get(route('admin_hr.unit_bisnis.index'))
            ->assertForbidden();
        $this->actingAs($atasan)
            ->get(route('admin_hr.unit_bisnis.create'))
            ->assertForbidden();
        $this->actingAs($karyawan)
            ->post(route('admin_hr.unit_bisnis.store'), $this->dataUnit())
            ->assertForbidden();
    }

    public function test_admin_hr_dapat_menambah_unit_bisnis(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);

        $this->actingAs($adminHr)
            ->post(route('admin_hr.unit_bisnis.store'), $this->dataUnit([
                'kode' => ' kuma-baru ',
                'nama' => ' Klinik Utama Baru ',
            ]))
            ->assertRedirect(route('admin_hr.unit_bisnis.index'))
            ->assertSessionHas('sukses');

        $this->assertDatabaseHas('unit_bisnis', [
            'kode' => 'KUMA-BARU',
            'nama' => 'Klinik Utama Baru',
            'kategori' => KategoriUnitBisnis::Operasional->value,
            'aktif' => true,
        ]);
    }

    public function test_data_unit_bisnis_divalidasi(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        UnitBisnis::factory()->create([
            'kode' => 'KUMA',
            'nama' => 'Klinik Utama Medika Antapani',
        ]);

        $this->actingAs($adminHr)
            ->from(route('admin_hr.unit_bisnis.create'))
            ->post(route('admin_hr.unit_bisnis.store'), [
                'kode' => 'KUMA',
                'nama' => 'Klinik Utama Medika Antapani',
                'kategori' => 'bukan_kategori',
                'aktif' => '1',
            ])
            ->assertRedirect(route('admin_hr.unit_bisnis.create'))
            ->assertSessionHasErrors(['kode', 'nama', 'kategori']);
    }

    public function test_admin_hr_dapat_memperbarui_dan_menonaktifkan_unit(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unit = UnitBisnis::factory()->create();

        $this->actingAs($adminHr)
            ->put(route('admin_hr.unit_bisnis.update', $unit), $this->dataUnit([
                'kode' => 'HO',
                'nama' => 'Head Office',
                'kategori' => KategoriUnitBisnis::HeadOffice->value,
                'aktif' => '0',
            ]))
            ->assertRedirect(route('admin_hr.unit_bisnis.index'))
            ->assertSessionHas('sukses');

        $this->assertDatabaseHas('unit_bisnis', [
            'id' => $unit->getKey(),
            'kode' => 'HO',
            'nama' => 'Head Office',
            'kategori' => KategoriUnitBisnis::HeadOffice->value,
            'aktif' => false,
        ]);
    }

    public function test_daftar_unit_dapat_dicari_berdasarkan_kode_atau_nama(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        UnitBisnis::factory()->create(['kode' => 'MEDLAB', 'nama' => 'Medika Laboratorium']);
        UnitBisnis::factory()->create(['kode' => 'APOTEK', 'nama' => 'Apotek Medika Antapani']);

        $this->actingAs($adminHr)
            ->get(route('admin_hr.unit_bisnis.index', ['cari' => 'laboratorium']))
            ->assertOk()
            ->assertSee('Medika Laboratorium')
            ->assertDontSee('Apotek Medika Antapani');
    }

    /**
     * @param  array<string, string>  $pengganti
     * @return array<string, string>
     */
    private function dataUnit(array $pengganti = []): array
    {
        return array_replace([
            'kode' => 'UNIT-BARU',
            'nama' => 'Unit Bisnis Baru',
            'kategori' => KategoriUnitBisnis::Operasional->value,
            'aktif' => '1',
        ], $pengganti);
    }

    private function buatPengguna(PeranPengguna $peran): Pengguna
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);
        Pegawai::factory()->for($pengguna, 'pengguna')->create();

        return $pengguna;
    }
}
