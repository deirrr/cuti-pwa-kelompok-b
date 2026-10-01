<?php

namespace Tests\Feature;

use App\Enums\KeputusanPersetujuan;
use App\Enums\PeranPengguna;
use App\Enums\StatusPengajuanCuti;
use App\Enums\TahapPersetujuan;
use App\Models\BagianOrganisasi;
use App\Models\HariLibur;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AlurPengajuanCutiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->travelTo('2026-10-01 08:00:00');
    }

    public function test_satu_kali_input_beberapa_tanggal_membuat_pengajuan_terpisah(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, $atasan);

        $this->actingAs($staff->pengguna)
            ->post(route('cuti.store'), [
                'tanggal_cuti' => ['2026-10-03', '2026-10-05', '2026-10-06'],
                'alasan' => 'Keperluan keluarga.',
            ])
            ->assertRedirect(route('cuti.index'));

        $this->assertSame(3, PengajuanCuti::query()->count());
        $this->assertSame(
            ['2026-10-03', '2026-10-05', '2026-10-06'],
            PengajuanCuti::query()
                ->orderBy('tanggal_cuti')
                ->get()
                ->map(fn (PengajuanCuti $pengajuan): string => $pengajuan->tanggal_cuti->toDateString())
                ->all(),
        );
        $this->assertSame(
            [StatusPengajuanCuti::MenungguAtasan, StatusPengajuanCuti::MenungguAtasan, StatusPengajuanCuti::MenungguAtasan],
            PengajuanCuti::query()->orderBy('tanggal_cuti')->get()->pluck('status')->all(),
        );
    }

    public function test_setiap_tanggal_dapat_diputuskan_secara_independen(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, $atasan);
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr, $bagian);
        $this->actingAs($staff->pengguna)->post(route('cuti.store'), [
            'tanggal_cuti' => ['2026-10-03', '2026-10-05', '2026-10-06'],
            'alasan' => 'Keperluan keluarga.',
        ]);
        $tanggalTiga = PengajuanCuti::query()->whereDate('tanggal_cuti', '2026-10-03')->firstOrFail();
        $tanggalLima = PengajuanCuti::query()->whereDate('tanggal_cuti', '2026-10-05')->firstOrFail();
        $tanggalEnam = PengajuanCuti::query()->whereDate('tanggal_cuti', '2026-10-06')->firstOrFail();

        $this->actingAs($atasan->pengguna)
            ->post(route('persetujuan_atasan.store', $tanggalTiga), [
                'keputusan' => KeputusanPersetujuan::Ditolak->value,
                'catatan' => 'Jadwal tidak memungkinkan.',
            ])
            ->assertRedirect();
        $this->actingAs($atasan->pengguna)
            ->post(route('persetujuan_atasan.store', $tanggalLima), [
                'keputusan' => KeputusanPersetujuan::Disetujui->value,
                'catatan' => '',
            ])
            ->assertRedirect();
        $this->actingAs($adminHr->pengguna)
            ->post(route('admin_hr.persetujuan.store', $tanggalLima), [
                'keputusan' => KeputusanPersetujuan::Disetujui->value,
                'catatan' => '',
            ])
            ->assertRedirect();

        $this->assertSame(StatusPengajuanCuti::Ditolak, $tanggalTiga->refresh()->status);
        $this->assertSame(StatusPengajuanCuti::Disetujui, $tanggalLima->refresh()->status);
        $this->assertSame(StatusPengajuanCuti::MenungguAtasan, $tanggalEnam->refresh()->status);
        $this->assertDatabaseHas('saldo_cuti', [
            'pegawai_id' => $staff->getKey(),
            'tahun' => 2026,
            'saldo_tersedia' => 11,
        ]);
    }

    public function test_pengajuan_belum_diputuskan_dapat_diubah_dan_dibatalkan(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, $atasan);
        $this->actingAs($staff->pengguna)->post(route('cuti.store'), [
            'tanggal_cuti' => ['2026-10-03'],
            'alasan' => 'Keperluan awal.',
        ]);
        $pengajuan = PengajuanCuti::query()->firstOrFail();

        $this->actingAs($staff->pengguna)
            ->put(route('cuti.update', $pengajuan), [
                'tanggal_cuti' => ['2026-10-05'],
                'alasan' => 'Keperluan yang diperbarui.',
            ])
            ->assertRedirect(route('cuti.index'));

        $this->assertSame('2026-10-05', $pengajuan->refresh()->tanggal_cuti->toDateString());
        $this->assertSame('Keperluan yang diperbarui.', $pengajuan->alasan);

        $this->actingAs($staff->pengguna)
            ->post(route('cuti.cancel', $pengajuan))
            ->assertRedirect();

        $this->assertSame(StatusPengajuanCuti::Dibatalkan, $pengajuan->refresh()->status);
        $this->assertNotNull($pengajuan->dibatalkan_pada);
    }

    public function test_pengajuan_yang_sudah_mendapat_keputusan_tidak_dapat_diubah_atau_dibatalkan(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, $atasan);
        $pengajuan = PengajuanCuti::factory()->menungguHr()->create([
            'pegawai_id' => $staff->getKey(),
            'atasan_penyetuju_id' => $atasan->pengguna_id,
            'tanggal_cuti' => '2026-10-05',
        ]);
        $pengajuan->persetujuan()->create([
            'pemberi_keputusan_id' => $atasan->pengguna_id,
            'tahap' => TahapPersetujuan::Atasan,
            'keputusan' => KeputusanPersetujuan::Disetujui,
            'diputuskan_pada' => now(),
        ]);

        $this->actingAs($staff->pengguna)
            ->put(route('cuti.update', $pengajuan), [
                'tanggal_cuti' => ['2026-10-06'],
                'alasan' => 'Mencoba mengubah.',
            ])
            ->assertForbidden();
        $this->actingAs($staff->pengguna)
            ->post(route('cuti.cancel', $pengajuan))
            ->assertForbidden();

        $this->assertSame('2026-10-05', $pengajuan->refresh()->tanggal_cuti->toDateString());
        $this->assertSame(StatusPengajuanCuti::MenungguHr, $pengajuan->status);
    }

    public function test_hari_minggu_dan_hari_libur_nasional_tidak_dapat_dipilih(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $bagian, $atasan);
        HariLibur::factory()->create([
            'tanggal' => '2026-10-05',
            'nama' => 'Hari Libur Nasional Contoh',
        ]);

        $this->actingAs($staff->pengguna)
            ->from(route('cuti.create'))
            ->post(route('cuti.store'), [
                'tanggal_cuti' => ['2026-10-04'],
                'alasan' => 'Memilih hari Minggu.',
            ])
            ->assertRedirect(route('cuti.create'))
            ->assertSessionHasErrors([
                'tanggal_cuti' => 'Hari Minggu tidak dapat dipilih sebagai tanggal cuti.',
            ]);

        $this->actingAs($staff->pengguna)
            ->from(route('cuti.create'))
            ->post(route('cuti.store'), [
                'tanggal_cuti' => ['2026-10-05'],
                'alasan' => 'Memilih hari libur nasional.',
            ])
            ->assertRedirect(route('cuti.create'))
            ->assertSessionHasErrors('tanggal_cuti');

        $this->assertSame(0, PengajuanCuti::query()->count());
    }

    public function test_pengajuan_atasan_langsung_menunggu_hr(): void
    {
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan, $bagian);

        $this->actingAs($atasan->pengguna)
            ->post(route('cuti.store'), [
                'tanggal_cuti' => ['2026-10-15'],
                'alasan' => 'Keperluan pribadi.',
            ])
            ->assertRedirect(route('cuti.index'));

        $pengajuan = PengajuanCuti::query()->firstOrFail();

        $this->assertSame(StatusPengajuanCuti::MenungguHr, $pengajuan->status);
        $this->assertNull($pengajuan->atasan_penyetuju_id);
    }

    private function buatPegawai(
        PeranPengguna $peran,
        BagianOrganisasi $bagian,
        ?Pegawai $atasan = null,
    ): Pegawai {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);

        return Pegawai::factory()
            ->for($pengguna, 'pengguna')
            ->for($bagian, 'bagianOrganisasi')
            ->create([
                'atasan_id' => $atasan?->getKey(),
                'jatah_cuti' => 12,
            ]);
    }
}
