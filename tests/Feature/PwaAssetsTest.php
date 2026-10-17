<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaAssetsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_halaman_login_memuat_konfigurasi_dan_tombol_instalasi_pwa(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('manifest.json', false)
            ->assertSee('service-worker-url', false)
            ->assertSee('data-pwa-install', false)
            ->assertSee('Pasang Medika Cuti')
            ->assertSee('Buka Medika Cuti dari ikon pada layar utama.');
    }

    public function test_manifest_memiliki_identitas_dan_ikon_yang_dibutuhkan(): void
    {
        $manifestPath = public_path('manifest.json');
        $manifest = json_decode((string) file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('Medika Cuti - Klinik Utama Medika Antapani', $manifest['name']);
        $this->assertSame('Medika Cuti', $manifest['short_name']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('./dashboard', $manifest['start_url']);
        $this->assertSame(
            ['192x192', '512x512'],
            array_column($manifest['icons'], 'sizes'),
        );
    }

    public function test_ikon_dan_halaman_offline_tersedia(): void
    {
        $this->assertSame([192, 192], array_slice(getimagesize(public_path('icons/icon-192.png')), 0, 2));
        $this->assertSame([512, 512], array_slice(getimagesize(public_path('icons/icon-512.png')), 0, 2));
        $this->assertFileExists(public_path('sw.js'));
        $this->assertStringContainsString(
            'Tidak ada koneksi',
            (string) file_get_contents(public_path('offline.html')),
        );
    }

    public function test_service_worker_menangani_push_dan_klik_notifikasi(): void
    {
        $serviceWorker = (string) file_get_contents(public_path('sw.js'));

        $this->assertStringContainsString("addEventListener('push'", $serviceWorker);
        $this->assertStringContainsString("addEventListener('notificationclick'", $serviceWorker);
        $this->assertStringContainsString('showNotification', $serviceWorker);
        $this->assertStringContainsString('openWindow', $serviceWorker);
    }
}
