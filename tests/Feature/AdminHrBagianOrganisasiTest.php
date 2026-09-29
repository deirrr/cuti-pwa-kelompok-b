<?php

namespace Tests\Feature;

use App\Enums\JenisBagianOrganisasi;
use App\Enums\KategoriUnitBisnis;
use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\UnitBisnis;
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

    public function test_admin_hr_dapat_membuka_daftar_bagian_organisasi(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $bagian = BagianOrganisasi::factory()->create(['nama' => 'Pendaftaran']);

        $this->actingAs($adminHr)
            ->get(route('admin_hr.bagian_organisasi.index'))
            ->assertOk()
            ->assertSee('Bagian Organisasi')
            ->assertSee('Pendaftaran');

        $this->actingAs($adminHr)
            ->get(route('admin_hr.bagian_organisasi.create'))
            ->assertOk()
            ->assertSee('Tambah Bagian Organisasi');

        $this->actingAs($adminHr)
            ->get(route('admin_hr.bagian_organisasi.edit', $bagian))
            ->assertOk()
            ->assertSee('Ubah Pendaftaran');
    }

    public function test_pengguna_tanpa_peran_admin_hr_dilarang_mengelola_bagian_organisasi(): void
    {
        $karyawan = $this->buatPengguna(PeranPengguna::Karyawan);
        $atasan = $this->buatPengguna(PeranPengguna::Atasan);

        $this->actingAs($karyawan)
            ->get(route('admin_hr.bagian_organisasi.index'))
            ->assertForbidden();

        $this->actingAs($atasan)
            ->post(route('admin_hr.bagian_organisasi.store'), [])
            ->assertForbidden();
    }

    public function test_admin_hr_dapat_menambah_bagian_pada_unit_operasional(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unit = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::Operasional]);

        $this->actingAs($adminHr)
            ->post(route('admin_hr.bagian_organisasi.store'), $this->dataBagian($unit, [
                'kode' => ' perawat-gigi ',
                'nama' => ' Perawat Gigi ',
            ]))
            ->assertRedirect(route('admin_hr.bagian_organisasi.index'))
            ->assertSessionHas('sukses');

        $this->assertDatabaseHas('bagian_organisasi', [
            'unit_bisnis_id' => $unit->getKey(),
            'jenis' => JenisBagianOrganisasi::Bagian->value,
            'kode' => 'PERAWAT-GIGI',
            'nama' => 'Perawat Gigi',
            'aktif' => true,
        ]);
    }

    public function test_jenis_struktur_harus_sesuai_dengan_kategori_unit(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unitOperasional = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::Operasional]);

        $this->actingAs($adminHr)
            ->from(route('admin_hr.bagian_organisasi.create'))
            ->post(route('admin_hr.bagian_organisasi.store'), $this->dataBagian($unitOperasional, [
                'jenis' => JenisBagianOrganisasi::Departemen->value,
            ]))
            ->assertRedirect(route('admin_hr.bagian_organisasi.create'))
            ->assertSessionHasErrors(['jenis' => 'Unit operasional hanya dapat menggunakan jenis Bagian.']);

        $this->assertDatabaseMissing('bagian_organisasi', ['kode' => 'BAGIAN-BARU']);
    }

    public function test_induk_harus_berada_pada_unit_bisnis_yang_sama(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unitTujuan = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::Operasional]);
        $indukDariUnitLain = BagianOrganisasi::factory()->create();

        $this->actingAs($adminHr)
            ->post(route('admin_hr.bagian_organisasi.store'), $this->dataBagian($unitTujuan, [
                'induk_id' => (string) $indukDariUnitLain->getKey(),
            ]))
            ->assertSessionHasErrors(['induk_id' => 'Induk harus berada pada unit bisnis yang sama.']);
    }

    public function test_admin_hr_dapat_memperbarui_dan_menonaktifkan_bagian_organisasi(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unit = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::Operasional]);
        $unitLain = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::Operasional]);
        $bagian = BagianOrganisasi::factory()->for($unit, 'unitBisnis')->create(['kode' => 'DAFTAR']);
        BagianOrganisasi::factory()->for($unitLain, 'unitBisnis')->create(['kode' => 'PENDAFTARAN']);

        $this->actingAs($adminHr)
            ->put(route('admin_hr.bagian_organisasi.update', $bagian), $this->dataBagian($unit, [
                'kode' => 'PENDAFTARAN',
                'nama' => 'Pendaftaran Klinik Utama',
                'aktif' => '0',
            ]))
            ->assertRedirect(route('admin_hr.bagian_organisasi.index'))
            ->assertSessionHas('sukses');

        $this->assertDatabaseHas('bagian_organisasi', [
            'id' => $bagian->getKey(),
            'unit_bisnis_id' => $unit->getKey(),
            'kode' => 'PENDAFTARAN',
            'nama' => 'Pendaftaran Klinik Utama',
            'aktif' => false,
        ]);
    }

    public function test_hierarki_bagian_organisasi_tidak_boleh_melingkar(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unit = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::HeadOffice]);
        $direktorat = BagianOrganisasi::factory()->for($unit, 'unitBisnis')->create([
            'jenis' => JenisBagianOrganisasi::Direktorat,
        ]);
        $departemen = BagianOrganisasi::factory()
            ->for($unit, 'unitBisnis')
            ->for($direktorat, 'induk')
            ->create(['jenis' => JenisBagianOrganisasi::Departemen]);

        $this->actingAs($adminHr)
            ->put(route('admin_hr.bagian_organisasi.update', $direktorat), $this->dataBagian($unit, [
                'induk_id' => (string) $departemen->getKey(),
                'jenis' => JenisBagianOrganisasi::Direktorat->value,
                'kode' => $direktorat->kode,
                'nama' => $direktorat->nama,
            ]))
            ->assertSessionHasErrors(['induk_id' => 'Induk tidak boleh membentuk hubungan melingkar.']);

        $this->assertNull($direktorat->refresh()->induk_id);
    }

    public function test_bagian_organisasi_yang_sudah_memiliki_anak_tidak_dapat_dipindahkan_unit(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unitAsal = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::Operasional]);
        $unitTujuan = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::Operasional]);
        $induk = BagianOrganisasi::factory()->for($unitAsal, 'unitBisnis')->create();
        BagianOrganisasi::factory()->for($unitAsal, 'unitBisnis')->for($induk, 'induk')->create();

        $this->actingAs($adminHr)
            ->put(route('admin_hr.bagian_organisasi.update', $induk), $this->dataBagian($unitTujuan, [
                'kode' => $induk->kode,
                'nama' => $induk->nama,
            ]))
            ->assertSessionHasErrors([
                'unit_bisnis_id' => 'Unit bisnis tidak dapat diubah karena bagian organisasi sudah digunakan.',
            ]);

        $this->assertSame($unitAsal->getKey(), $induk->refresh()->unit_bisnis_id);
    }

    public function test_daftar_dapat_disaring_dan_teks_nama_tetap_di_escape(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $unit = UnitBisnis::factory()->create(['kategori' => KategoriUnitBisnis::Operasional]);
        BagianOrganisasi::factory()->for($unit, 'unitBisnis')->create([
            'kode' => 'DAFTAR',
            'nama' => '<script>alert(1)</script> Pendaftaran',
        ]);
        BagianOrganisasi::factory()->create(['nama' => 'Bagian Unit Lain']);

        $this->actingAs($adminHr)
            ->get(route('admin_hr.bagian_organisasi.index', [
                'unit_bisnis_id' => $unit->getKey(),
                'cari' => 'Pendaftaran',
            ]))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt; Pendaftaran', false)
            ->assertDontSee('<script>alert(1)</script> Pendaftaran', false)
            ->assertDontSee('Bagian Unit Lain');
    }

    /**
     * @param  array<string, string>  $pengganti
     * @return array<string, string>
     */
    private function dataBagian(UnitBisnis $unitBisnis, array $pengganti = []): array
    {
        return array_replace([
            'unit_bisnis_id' => (string) $unitBisnis->getKey(),
            'induk_id' => '',
            'jenis' => JenisBagianOrganisasi::Bagian->value,
            'kode' => 'BAGIAN-BARU',
            'nama' => 'Bagian Baru',
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
