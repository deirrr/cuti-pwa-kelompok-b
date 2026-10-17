<?php

namespace Tests\Feature;

use App\Enums\KeputusanPersetujuan;
use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use App\Notifications\PengajuanCutiDiajukan;
use App\Notifications\StatusPengajuanCutiDiperbarui;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use Tests\TestCase;

class NotifikasiPengajuanCutiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->travelTo('2026-10-01 08:00:00');
    }

    public function test_pengajuan_staff_mengirim_notifikasi_kepada_atasan(): void
    {
        Notification::fake();
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, $atasan);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);

        $this->actingAs($staff->pengguna)
            ->post(route('cuti.store'), [
                'tanggal_cuti' => ['2026-10-05'],
                'alasan' => 'Keperluan keluarga.',
            ])
            ->assertRedirect(route('cuti.index'));

        Notification::assertSentTo($atasan->pengguna, PengajuanCutiDiajukan::class);
        Notification::assertNotSentTo($adminHr->pengguna, PengajuanCutiDiajukan::class);
        Notification::assertCount(1);
    }

    public function test_pengajuan_atasan_mengirim_notifikasi_hanya_kepada_admin_hr_aktif(): void
    {
        Notification::fake();
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $adminHrAktif = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $adminHrNonaktif = $this->buatPegawai(PeranPengguna::AdminHr, $bagian, null, false);

        $this->actingAs($atasan->pengguna)
            ->post(route('cuti.store'), [
                'tanggal_cuti' => ['2026-10-05'],
                'alasan' => 'Keperluan pribadi.',
            ])
            ->assertRedirect(route('cuti.index'));

        Notification::assertSentTo($adminHrAktif->pengguna, PengajuanCutiDiajukan::class);
        Notification::assertNotSentTo($adminHrNonaktif->pengguna, PengajuanCutiDiajukan::class);
        Notification::assertCount(1);
    }

    public function test_persetujuan_atasan_memberi_kabar_kepada_staff_dan_admin_hr(): void
    {
        Notification::fake();
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, $atasan);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $pengajuan = PengajuanCuti::factory()->menungguAtasan()->create([
            'pegawai_id' => $staff->getKey(),
            'atasan_penyetuju_id' => $atasan->pengguna_id,
            'tanggal_cuti' => '2026-10-05',
        ]);

        $this->actingAs($atasan->pengguna)
            ->post(route('persetujuan_atasan.store', $pengajuan), [
                'keputusan' => KeputusanPersetujuan::Disetujui->value,
                'catatan' => '',
            ])
            ->assertRedirect();

        Notification::assertSentTo($staff->pengguna, StatusPengajuanCutiDiperbarui::class);
        Notification::assertSentTo($adminHr->pengguna, PengajuanCutiDiajukan::class);
        Notification::assertCount(2);
    }

    public function test_penolakan_atasan_memberi_kabar_hanya_kepada_staff(): void
    {
        Notification::fake();
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, $atasan);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $pengajuan = PengajuanCuti::factory()->menungguAtasan()->create([
            'pegawai_id' => $staff->getKey(),
            'atasan_penyetuju_id' => $atasan->pengguna_id,
            'tanggal_cuti' => '2026-10-05',
        ]);

        $this->actingAs($atasan->pengguna)
            ->post(route('persetujuan_atasan.store', $pengajuan), [
                'keputusan' => KeputusanPersetujuan::Ditolak->value,
                'catatan' => 'Jadwal tidak memungkinkan.',
            ])
            ->assertRedirect();

        Notification::assertSentTo($staff->pengguna, StatusPengajuanCutiDiperbarui::class);
        Notification::assertNotSentTo($adminHr->pengguna, PengajuanCutiDiajukan::class);
        Notification::assertCount(1);
    }

    public function test_keputusan_admin_hr_memberi_kabar_kepada_pengaju(): void
    {
        Notification::fake();
        $bagian = BagianOrganisasi::factory()->create();
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $pengajuan = PengajuanCuti::factory()->menungguHr()->create([
            'pegawai_id' => $staff->getKey(),
            'tanggal_cuti' => '2026-10-05',
        ]);

        $this->actingAs($adminHr->pengguna)
            ->post(route('admin_hr.persetujuan.store', $pengajuan), [
                'keputusan' => KeputusanPersetujuan::Ditolak->value,
                'catatan' => 'Data belum sesuai.',
            ])
            ->assertRedirect();

        Notification::assertSentTo($staff->pengguna, StatusPengajuanCutiDiperbarui::class);
        Notification::assertCount(1);
    }

    public function test_pengguna_dapat_melihat_dan_membaca_notifikasi_miliknya(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);
        $pengajuan = PengajuanCuti::factory()->ditolak()->create([
            'pegawai_id' => $staff->getKey(),
            'tanggal_cuti' => '2026-10-05',
        ]);
        $staff->pengguna->notify(new StatusPengajuanCutiDiperbarui(
            $pengajuan,
            'Pengajuan cuti ditolak',
            'Pengajuan cuti tanggal 05-10-2026 ditolak oleh Admin HR.',
            'cuti.index',
        ));
        $notifikasi = $staff->pengguna->notifications()->firstOrFail();

        $this->actingAs($staff->pengguna)
            ->get(route('notifikasi.index'))
            ->assertOk()
            ->assertSee('Pengajuan cuti ditolak')
            ->assertSee('Pengajuan cuti tanggal 05-10-2026 ditolak oleh Admin HR.');

        $this->actingAs($staff->pengguna)
            ->post(route('notifikasi.baca', $notifikasi->id))
            ->assertRedirect(route('cuti.index'));

        $this->assertNotNull($notifikasi->fresh()->read_at);
    }

    public function test_pengguna_tidak_dapat_membaca_notifikasi_milik_pengguna_lain(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $pemilik = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);
        $penggunaLain = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);
        $pengajuan = PengajuanCuti::factory()->ditolak()->create([
            'pegawai_id' => $pemilik->getKey(),
            'tanggal_cuti' => '2026-10-05',
        ]);
        $pemilik->pengguna->notify(new StatusPengajuanCutiDiperbarui(
            $pengajuan,
            'Pengajuan cuti ditolak',
            'Pengajuan cuti ditolak oleh Admin HR.',
            'cuti.index',
        ));
        $notifikasi = $pemilik->pengguna->notifications()->firstOrFail();

        $this->actingAs($penggunaLain->pengguna)
            ->post(route('notifikasi.baca', $notifikasi->id))
            ->assertNotFound();

        $this->assertNull($notifikasi->fresh()->read_at);
    }

    public function test_notifikasi_status_juga_dikirim_melalui_web_push(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian);
        $pengajuan = PengajuanCuti::factory()->ditolak()->create([
            'pegawai_id' => $staff->getKey(),
            'tanggal_cuti' => '2026-10-05',
        ]);
        $notifikasi = new StatusPengajuanCutiDiperbarui(
            $pengajuan,
            'Pengajuan cuti ditolak',
            'Pengajuan cuti tanggal 05-10-2026 ditolak oleh Admin HR.',
            'cuti.index',
        );

        $this->assertContains(WebPushChannel::class, $notifikasi->via($staff->pengguna));
        $this->assertSame([
            'judul' => 'Pengajuan cuti ditolak',
            'pesan' => 'Pengajuan cuti tanggal 05-10-2026 ditolak oleh Admin HR.',
            'jenis' => 'status_diperbarui',
            'pengajuan_cuti_id' => $pengajuan->getKey(),
            'route_tujuan' => 'cuti.index',
        ], $notifikasi->toArray($staff->pengguna));

        $pesanPush = $notifikasi->toWebPush($staff->pengguna, $notifikasi)->toArray();

        $this->assertSame('Pengajuan cuti ditolak', $pesanPush['title']);
        $this->assertSame('/cuti', $pesanPush['data']['url']);
    }

    private function buatPegawai(
        PeranPengguna $peran,
        BagianOrganisasi $bagian,
        ?Pegawai $atasan = null,
        bool $aktif = true,
    ): Pegawai {
        $pengguna = Pengguna::factory()->create([
            'peran' => $peran,
            'aktif' => $aktif,
        ]);

        return Pegawai::factory()
            ->for($pengguna, 'pengguna')
            ->for($bagian, 'bagianOrganisasi')
            ->create([
                'atasan_id' => $atasan?->getKey(),
                'jatah_cuti' => 12,
            ]);
    }
}
