<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AutentikasiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_halaman_masuk_meminta_nik_dan_kata_sandi(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('NIK')
            ->assertSee('Kata sandi');
    }

    public function test_nik_dan_kata_sandi_wajib_diisi(): void
    {
        $response = $this->from(route('login'))
            ->post(route('login.store'));

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'nomor_induk' => 'NIK wajib diisi.',
                'kata_sandi' => 'Kata sandi wajib diisi.',
            ]);
        $this->assertGuest();
    }

    public function test_pengguna_aktif_dapat_masuk_menggunakan_nik_dan_kata_sandi(): void
    {
        $pegawai = $this->buatPegawai(nomorInduk: 'pgw-001234');

        $response = $this->post(route('login.store'), [
            'nomor_induk' => strtolower($pegawai->nomor_induk),
            'kata_sandi' => 'kata-sandi-benar',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertSame('PGW-001234', $pegawai->nomor_induk);
        $this->assertAuthenticatedAs($pegawai->pengguna);
        $this->assertNotNull($pegawai->pengguna->refresh()->terakhir_masuk_pada);
    }

    public function test_kredensial_salah_tidak_membuat_sesi_pengguna(): void
    {
        $pegawai = $this->buatPegawai();

        $response = $this->from(route('login'))->post(route('login.store'), [
            'nomor_induk' => $pegawai->nomor_induk,
            'kata_sandi' => 'kata-sandi-salah',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'nomor_induk' => 'NIK atau kata sandi tidak sesuai, atau akun tidak aktif.',
            ]);
        $this->assertGuest();
    }

    public function test_akun_pengguna_nonaktif_tidak_dapat_masuk(): void
    {
        $pegawai = $this->buatPegawai(penggunaAktif: false);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'nomor_induk' => $pegawai->nomor_induk,
            'kata_sandi' => 'kata-sandi-benar',
        ]);

        $response->assertSessionHasErrors('nomor_induk');
        $this->assertGuest();
    }

    public function test_data_pegawai_nonaktif_tidak_dapat_digunakan_untuk_masuk(): void
    {
        $pegawai = $this->buatPegawai(pegawaiAktif: false);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'nomor_induk' => $pegawai->nomor_induk,
            'kata_sandi' => 'kata-sandi-benar',
        ]);

        $response->assertSessionHasErrors('nomor_induk');
        $this->assertGuest();
    }

    public function test_tamu_yang_membuka_dashboard_diarahkan_ke_halaman_masuk(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_pengguna_dapat_keluar_dan_sesinya_dihapus(): void
    {
        $pegawai = $this->buatPegawai();

        $response = $this->actingAs($pegawai->pengguna)
            ->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_sesi_dihentikan_jika_akun_dinonaktifkan_setelah_masuk(): void
    {
        $pegawai = $this->buatPegawai();
        $pegawai->pengguna->update(['aktif' => false]);

        $response = $this->actingAs($pegawai->pengguna)
            ->get(route('dashboard'));

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('nomor_induk');
        $this->assertGuest();
    }

    public function test_percobaan_masuk_berulang_dibatasi(): void
    {
        $pegawai = $this->buatPegawai();
        $kunciPembatas = strtolower($pegawai->nomor_induk).'|127.0.0.1';
        RateLimiter::clear($kunciPembatas);

        for ($percobaan = 0; $percobaan < 5; $percobaan++) {
            $this->post(route('login.store'), [
                'nomor_induk' => $pegawai->nomor_induk,
                'kata_sandi' => 'kata-sandi-salah',
            ]);
        }

        $response = $this->from(route('login'))->post(route('login.store'), [
            'nomor_induk' => $pegawai->nomor_induk,
            'kata_sandi' => 'kata-sandi-benar',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('nomor_induk');
        $this->assertGuest();

        RateLimiter::clear($kunciPembatas);
    }

    private function buatPegawai(
        PeranPengguna $peran = PeranPengguna::Karyawan,
        bool $penggunaAktif = true,
        bool $pegawaiAktif = true,
        ?string $nomorInduk = null,
    ): Pegawai {
        $pengguna = Pengguna::factory()->create([
            'kata_sandi' => 'kata-sandi-benar',
            'peran' => $peran,
            'aktif' => $penggunaAktif,
        ]);

        return Pegawai::factory()
            ->for($pengguna, 'pengguna')
            ->create([
                'aktif' => $pegawaiAktif,
                ...($nomorInduk === null ? [] : ['nomor_induk' => $nomorInduk]),
            ]);
    }
}
