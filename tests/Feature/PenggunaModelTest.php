<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PenggunaModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_pengguna_dapat_divalidasi_menggunakan_kata_sandi(): void
    {
        $pengguna = Pengguna::factory()->create([
            'email' => 'karyawan@example.test',
            'kata_sandi' => 'rahasia-kuat',
        ]);

        $this->assertTrue(Auth::validate([
            'email' => $pengguna->email,
            'password' => 'rahasia-kuat',
        ]));
        $this->assertFalse(Auth::validate([
            'email' => $pengguna->email,
            'password' => 'kata-sandi-salah',
        ]));
        $this->assertSame('pengguna', $pengguna->getTable());
        $this->assertSame('kata_sandi', $pengguna->getAuthPasswordName());
        $this->assertSame('token_ingat', $pengguna->getRememberTokenName());
    }

    public function test_atribut_pengguna_memiliki_cast_dan_perlindungan_yang_tepat(): void
    {
        $pengguna = Pengguna::factory()->create([
            'peran' => PeranPengguna::AdminHr,
            'aktif' => true,
        ]);

        $this->assertSame(PeranPengguna::AdminHr, $pengguna->peran);
        $this->assertTrue($pengguna->aktif);
        $this->assertArrayNotHasKey('kata_sandi', $pengguna->toArray());
        $this->assertArrayNotHasKey('token_ingat', $pengguna->toArray());
    }
}
