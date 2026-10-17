<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LanggananPushTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_pengguna_harus_masuk_untuk_mengelola_langganan_push(): void
    {
        $this->postJson(route('notifikasi_push.store'), $this->payloadLangganan())
            ->assertUnauthorized();

        $this->deleteJson(route('notifikasi_push.destroy'), [
            'endpoint' => 'https://push.example.test/subscriptions/device-1',
        ])->assertUnauthorized();
    }

    public function test_pengguna_dapat_menyimpan_dan_memperbarui_langganan_push(): void
    {
        $pengguna = $this->buatPenggunaAktif();

        $this->actingAs($pengguna)
            ->postJson(route('notifikasi_push.store'), $this->payloadLangganan())
            ->assertOk()
            ->assertJsonPath('message', 'Notifikasi perangkat berhasil diaktifkan.');

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_type' => Pengguna::class,
            'subscribable_id' => $pengguna->getKey(),
            'endpoint' => 'https://push.example.test/subscriptions/device-1',
            'public_key' => 'public-key-1',
            'auth_token' => 'auth-token-1',
            'content_encoding' => 'aes128gcm',
        ]);

        $payload = $this->payloadLangganan();
        $payload['keys']['auth'] = 'auth-token-diperbarui';

        $this->actingAs($pengguna)
            ->postJson(route('notifikasi_push.store'), $payload)
            ->assertOk();

        $this->assertDatabaseCount('push_subscriptions', 1);
        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://push.example.test/subscriptions/device-1',
            'auth_token' => 'auth-token-diperbarui',
        ]);
    }

    public function test_pengguna_hanya_dapat_menghapus_langganan_push_miliknya(): void
    {
        $pemilik = $this->buatPenggunaAktif();
        $penggunaLain = $this->buatPenggunaAktif();
        $endpoint = 'https://push.example.test/subscriptions/device-1';

        $pemilik->updatePushSubscription($endpoint, 'public-key-1', 'auth-token-1', 'aes128gcm');

        $this->actingAs($penggunaLain)
            ->deleteJson(route('notifikasi_push.destroy'), ['endpoint' => $endpoint])
            ->assertOk();

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $pemilik->getKey(),
            'endpoint' => $endpoint,
        ]);

        $this->actingAs($pemilik)
            ->deleteJson(route('notifikasi_push.destroy'), ['endpoint' => $endpoint])
            ->assertOk()
            ->assertJsonPath('message', 'Notifikasi perangkat berhasil dinonaktifkan.');

        $this->assertDatabaseMissing('push_subscriptions', ['endpoint' => $endpoint]);
    }

    public function test_payload_langganan_push_harus_valid(): void
    {
        $pengguna = $this->buatPenggunaAktif();

        $this->actingAs($pengguna)
            ->postJson(route('notifikasi_push.store'), [
                'endpoint' => 'bukan-url',
                'keys' => [],
                'content_encoding' => 'tidak-didukung',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'endpoint',
                'keys.p256dh',
                'keys.auth',
                'content_encoding',
            ]);
    }

    public function test_halaman_notifikasi_memuat_pengaturan_push(): void
    {
        config()->set('webpush.vapid.public_key', 'vapid-public-key');
        $pengguna = $this->buatPenggunaAktif();

        $this->actingAs($pengguna)
            ->get(route('notifikasi.index'))
            ->assertOk()
            ->assertSee('Notifikasi perangkat')
            ->assertSee('data-push-notifications', false)
            ->assertSee('vapid-public-key', false)
            ->assertSee(route('notifikasi_push.store'), false)
            ->assertSee(route('notifikasi_push.destroy'), false);
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadLangganan(): array
    {
        return [
            'endpoint' => 'https://push.example.test/subscriptions/device-1',
            'keys' => [
                'p256dh' => 'public-key-1',
                'auth' => 'auth-token-1',
            ],
            'content_encoding' => 'aes128gcm',
        ];
    }

    private function buatPenggunaAktif(): Pengguna
    {
        return Pengguna::factory()
            ->hasPegawai()
            ->create();
    }
}
