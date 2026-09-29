<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminHrBagianOrganisasiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_hr_dapat_mengelola_bagian_organisasi(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);

        $this->actingAs($adminHr)
            ->post(route('admin_hr.bagian_organisasi.store'), [
                'kode' => ' farmasi ',
                'nama' => ' Instalasi Farmasi ',
                'aktif' => '1',
            ])
            ->assertRedirect(route('admin_hr.bagian_organisasi.index'))
            ->assertSessionHas('sukses');

        $bagian = BagianOrganisasi::query()->where('kode', 'FARMASI')->firstOrFail();

        $this->actingAs($adminHr)
            ->put(route('admin_hr.bagian_organisasi.update', $bagian), [
                'kode' => 'FARMASI',
                'nama' => 'Farmasi',
                'aktif' => '0',
            ])
            ->assertRedirect(route('admin_hr.bagian_organisasi.index'));

        $this->assertDatabaseHas('bagian_organisasi', [
            'id' => $bagian->getKey(),
            'nama' => 'Farmasi',
            'aktif' => false,
        ]);
    }

    public function test_pengguna_tanpa_peran_admin_hr_dilarang_mengelola_bagian_organisasi(): void
    {
        $staff = $this->buatPengguna(PeranPengguna::Karyawan);

        $this->actingAs($staff)
            ->post(route('admin_hr.bagian_organisasi.store'), [])
            ->assertForbidden();
    }

    public function test_kode_dan_nama_bagian_harus_unik(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        BagianOrganisasi::factory()->create(['kode' => 'FARMASI', 'nama' => 'Farmasi']);

        $this->actingAs($adminHr)
            ->post(route('admin_hr.bagian_organisasi.store'), [
                'kode' => 'FARMASI',
                'nama' => 'Farmasi',
                'aktif' => '1',
            ])
            ->assertSessionHasErrors(['kode', 'nama']);
    }

    private function buatPengguna(PeranPengguna $peran): Pengguna
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);
        Pegawai::factory()->for($pengguna, 'pengguna')->create();

        return $pengguna;
    }
}
