<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminHrSaldoCutiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_hr_dapat_mengatur_jatah_cuti_tahunan_pegawai(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $pegawai = Pegawai::factory()->create();

        $this->actingAs($adminHr)
            ->put(route('admin_hr.saldo_cuti.update', $pegawai), [
                'tahun' => '2026',
                'jatah_awal' => '15',
                'saldo_tersedia' => '13',
                'catatan' => 'Penyesuaian masa kerja.',
            ])
            ->assertRedirect(route('admin_hr.saldo_cuti.index', ['tahun' => 2026]))
            ->assertSessionHas('sukses');

        $this->assertDatabaseHas('saldo_cuti', [
            'pegawai_id' => $pegawai->getKey(),
            'tahun' => 2026,
            'jatah_awal' => 15,
            'saldo_tersedia' => 13,
            'catatan' => 'Penyesuaian masa kerja.',
        ]);
    }

    public function test_saldo_tersedia_tidak_boleh_melebihi_jatah_awal(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $pegawai = Pegawai::factory()->create();

        $this->actingAs($adminHr)
            ->put(route('admin_hr.saldo_cuti.update', $pegawai), [
                'tahun' => '2026',
                'jatah_awal' => '12',
                'saldo_tersedia' => '13',
            ])
            ->assertSessionHasErrors([
                'saldo_tersedia' => 'Saldo tersedia tidak boleh melebihi jatah awal.',
            ]);

        $this->assertDatabaseMissing('saldo_cuti', [
            'pegawai_id' => $pegawai->getKey(),
            'tahun' => 2026,
        ]);
    }

    public function test_pengguna_non_hr_dilarang_mengatur_saldo_cuti(): void
    {
        $staff = $this->buatPengguna(PeranPengguna::Karyawan);
        $pegawai = Pegawai::factory()->create();

        $this->actingAs($staff)
            ->put(route('admin_hr.saldo_cuti.update', $pegawai), [])
            ->assertForbidden();
    }

    public function test_hr_tidak_dapat_diberi_saldo_cuti_melalui_modul_ini(): void
    {
        $adminHr = $this->buatPengguna(PeranPengguna::AdminHr);
        $pegawaiHr = Pegawai::factory()
            ->for(Pengguna::factory()->adminHr(), 'pengguna')
            ->create();

        $this->actingAs($adminHr)
            ->put(route('admin_hr.saldo_cuti.update', $pegawaiHr), [
                'tahun' => '2026',
                'jatah_awal' => '12',
                'saldo_tersedia' => '12',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('saldo_cuti', [
            'pegawai_id' => $pegawaiHr->getKey(),
            'tahun' => 2026,
        ]);
    }

    private function buatPengguna(PeranPengguna $peran): Pengguna
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);
        Pegawai::factory()->for($pengguna, 'pengguna')->create();

        return $pengguna;
    }
}
