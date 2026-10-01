<?php

namespace Tests\Feature;

use App\Enums\JenisHariLibur;
use App\Enums\PeranPengguna;
use App\Models\HariLibur;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminHrHariLiburTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_hr_dapat_menambah_mengubah_dan_menghapus_hari_libur_nasional(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);

        $this->actingAs($adminHr)
            ->post(route('admin_hr.hari_libur.store'), [
                'tanggal' => '2026-12-25',
                'nama' => ' Hari Raya Natal ',
                'aktif' => '1',
            ])
            ->assertRedirect(route('admin_hr.hari_libur.index', ['tahun' => 2026]))
            ->assertSessionHas('sukses');

        $hariLibur = HariLibur::query()->whereDate('tanggal', '2026-12-25')->firstOrFail();

        $this->assertSame(JenisHariLibur::Nasional, $hariLibur->jenis);
        $this->assertSame('Hari Raya Natal', $hariLibur->nama);

        $this->actingAs($adminHr)
            ->put(route('admin_hr.hari_libur.update', $hariLibur), [
                'tanggal' => '2026-12-24',
                'nama' => 'Cuti Bersama Natal',
                'aktif' => '0',
            ])
            ->assertRedirect(route('admin_hr.hari_libur.index', ['tahun' => 2026]));

        $hariLibur->refresh();

        $this->assertSame('2026-12-24', $hariLibur->tanggal->toDateString());
        $this->assertSame('Cuti Bersama Natal', $hariLibur->nama);
        $this->assertFalse($hariLibur->aktif);

        $this->actingAs($adminHr)
            ->delete(route('admin_hr.hari_libur.destroy', $hariLibur))
            ->assertRedirect(route('admin_hr.hari_libur.index', ['tahun' => 2026]));

        $this->assertModelMissing($hariLibur);
    }

    public function test_tanggal_hari_libur_harus_unik(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        HariLibur::factory()->create(['tanggal' => '2026-12-25']);

        $this->actingAs($adminHr)
            ->post(route('admin_hr.hari_libur.store'), [
                'tanggal' => '2026-12-25',
                'nama' => 'Libur Lain',
                'aktif' => '1',
            ])
            ->assertSessionHasErrors(['tanggal']);
    }

    public function test_staff_dilarang_mengelola_hari_libur(): void
    {
        $staff = $this->buatPengguna(PeranPengguna::Karyawan);

        $this->actingAs($staff)
            ->post(route('admin_hr.hari_libur.store'), [])
            ->assertForbidden();
    }

    public function test_hari_libur_aktif_dari_pengaturan_hr_muncul_sebagai_tanggal_nonaktif_di_form_cuti(): void
    {
        $staff = $this->buatPengguna(PeranPengguna::Karyawan);
        HariLibur::factory()->create([
            'tanggal' => '2026-12-25',
            'nama' => 'Hari Raya Natal',
            'aktif' => true,
        ]);
        HariLibur::factory()->nonaktif()->create([
            'tanggal' => '2026-12-24',
        ]);

        $this->actingAs($staff)
            ->get(route('cuti.create'))
            ->assertOk()
            ->assertSee('2026-12-25')
            ->assertDontSee('2026-12-24');
    }

    private function buatPengguna(PeranPengguna $peran): Pengguna
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);
        Pegawai::factory()->for($pengguna, 'pengguna')->create();

        return $pengguna;
    }
}
