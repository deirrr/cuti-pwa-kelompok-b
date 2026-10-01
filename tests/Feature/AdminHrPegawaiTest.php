<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\SaldoCuti;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminHrPegawaiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_hr_dapat_membuka_halaman_master_karyawan(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.pegawai.index'))
            ->assertOk()
            ->assertSee('Master Karyawan')
            ->assertSee($adminHr->nama);

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.pegawai.create'))
            ->assertOk()
            ->assertSee('Tambah Karyawan');

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.pegawai.edit', $adminHr))
            ->assertOk()
            ->assertSee('Ubah '.$adminHr->nama);
    }

    public function test_semua_karyawan_ditampilkan_dalam_card_bagian_tanpa_pagination(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);
        $bagian = BagianOrganisasi::factory()->create([
            'kode' => 'FARMASI',
            'nama' => 'Farmasi',
        ]);

        foreach (range(1, 18) as $nomor) {
            Pegawai::factory()
                ->for($bagian, 'bagianOrganisasi')
                ->create(['nama' => sprintf('Pegawai Farmasi %02d', $nomor)]);
        }

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.pegawai.index'))
            ->assertOk()
            ->assertSee('data-bagian-card="'.$bagian->getKey().'"', false)
            ->assertSee('18 karyawan')
            ->assertSee('Pegawai Farmasi 01')
            ->assertSee('Pegawai Farmasi 18');
    }

    public function test_atasan_ditampilkan_lebih_dahulu_dan_detail_dibuka_melalui_dialog(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);
        $bagian = BagianOrganisasi::factory()->create();
        $atasan = $this->buatPegawai(PeranPengguna::Atasan);
        $atasan->update([
            'bagian_organisasi_id' => $bagian->getKey(),
            'nama' => 'Zeta Atasan',
        ]);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $atasan);
        $staff->update([
            'bagian_organisasi_id' => $bagian->getKey(),
            'nama' => 'Andi Staff',
        ]);

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.pegawai.index'))
            ->assertOk()
            ->assertSeeInOrder(['Zeta Atasan', 'Andi Staff'])
            ->assertSee('data-dialog-pegawai-open="dialog-pegawai-'.$staff->getKey().'"', false)
            ->assertSee('Detail karyawan')
            ->assertSee('href="'.route('admin_hr.pegawai.edit', $staff).'"', false)
            ->assertSee('Ubah Data');
    }

    public function test_admin_hr_dapat_menambahkan_atasan_dengan_jatah_cuti(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);
        $bagian = BagianOrganisasi::factory()->create();

        $this->actingAs($adminHr->pengguna)
            ->post(route('admin_hr.pegawai.store'), [
                'nomor_induk' => ' ats-001 ',
                'nama' => ' Atasan Farmasi ',
                'email' => 'ATASAN.FARMASI@EXAMPLE.TEST',
                'kata_sandi' => 'kata-sandi-awal',
                'kata_sandi_confirmation' => 'kata-sandi-awal',
                'bagian_organisasi_id' => $bagian->getKey(),
                'peran' => PeranPengguna::Atasan->value,
                'jatah_cuti' => '15',
                'tanggal_masuk' => '2026-01-02',
                'aktif' => '1',
            ])
            ->assertRedirect(route('admin_hr.pegawai.index'))
            ->assertSessionHas('sukses');

        $pegawai = Pegawai::query()->where('nomor_induk', 'ATS-001')->firstOrFail();

        $this->assertSame('Atasan Farmasi', $pegawai->nama);
        $this->assertSame(15, $pegawai->jatah_cuti);
        $this->assertSame(PeranPengguna::Atasan, $pegawai->pengguna->peran);
        $this->assertSame('atasan.farmasi@example.test', $pegawai->pengguna->email);
        $this->assertTrue(Hash::check('kata-sandi-awal', $pegawai->pengguna->kata_sandi));
        $this->assertDatabaseHas('pengguna_peran', [
            'pengguna_id' => $pegawai->pengguna_id,
            'peran_id' => $pegawai->pengguna->peranSistem()->where('kode', 'atasan')->value('peran.id'),
        ]);
        $this->assertDatabaseHas('saldo_cuti', [
            'pegawai_id' => $pegawai->getKey(),
            'tahun' => now()->year,
            'jatah_awal' => 15,
            'saldo_tersedia' => 15,
        ]);
    }

    public function test_staff_wajib_memiliki_atasan_langsung_yang_aktif(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);
        $bagian = BagianOrganisasi::factory()->create();

        $this->actingAs($adminHr->pengguna)
            ->post(route('admin_hr.pegawai.store'), $this->dataKaryawan($bagian, [
                'peran' => PeranPengguna::Karyawan->value,
                'atasan_id' => null,
            ]))
            ->assertSessionHasErrors('atasan_id');

        $bukanAtasan = $this->buatPegawai(PeranPengguna::Karyawan);

        $this->actingAs($adminHr->pengguna)
            ->post(route('admin_hr.pegawai.store'), $this->dataKaryawan($bagian, [
                'nomor_induk' => 'STF-002',
                'email' => 'staff.2@example.test',
                'peran' => PeranPengguna::Karyawan->value,
                'atasan_id' => $bukanAtasan->getKey(),
            ]))
            ->assertSessionHasErrors([
                'atasan_id' => 'Atasan langsung harus merupakan pegawai aktif dengan peran Atasan.',
            ]);
    }

    public function test_admin_hr_dapat_menambahkan_staff_dengan_atasan_langsung(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);
        $atasan = $this->buatPegawai(PeranPengguna::Atasan);
        $bagian = BagianOrganisasi::factory()->create();

        $this->actingAs($adminHr->pengguna)
            ->post(route('admin_hr.pegawai.store'), $this->dataKaryawan($bagian, [
                'atasan_id' => $atasan->getKey(),
            ]))
            ->assertRedirect(route('admin_hr.pegawai.index'));

        $staff = Pegawai::query()->where('nomor_induk', 'STF-001')->firstOrFail();

        $this->assertTrue($staff->atasan->is($atasan));
        $this->assertSame(PeranPengguna::Karyawan, $staff->pengguna->peran);
        $this->assertSame(12, $staff->jatah_cuti);
        $this->assertDatabaseHas('saldo_cuti', [
            'pegawai_id' => $staff->getKey(),
            'jatah_awal' => 12,
            'saldo_tersedia' => 12,
        ]);
    }

    public function test_perubahan_jatah_mempertahankan_cuti_yang_sudah_digunakan(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);
        $atasan = $this->buatPegawai(PeranPengguna::Atasan);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan, $atasan);
        SaldoCuti::factory()->for($staff)->create([
            'tahun' => now()->year,
            'jatah_awal' => 12,
            'saldo_tersedia' => 7,
        ]);

        $this->actingAs($adminHr->pengguna)
            ->put(route('admin_hr.pegawai.update', $staff), [
                'nomor_induk' => $staff->nomor_induk,
                'nama' => $staff->nama,
                'email' => $staff->pengguna->email,
                'kata_sandi' => '',
                'kata_sandi_confirmation' => '',
                'bagian_organisasi_id' => $staff->bagian_organisasi_id,
                'peran' => PeranPengguna::Karyawan->value,
                'atasan_id' => $atasan->getKey(),
                'jatah_cuti' => '15',
                'tanggal_masuk' => $staff->tanggal_masuk?->toDateString(),
                'aktif' => '1',
            ])
            ->assertRedirect(route('admin_hr.pegawai.index'));

        $this->assertSame(15, $staff->fresh()->jatah_cuti);
        $this->assertDatabaseHas('saldo_cuti', [
            'pegawai_id' => $staff->getKey(),
            'tahun' => now()->year,
            'jatah_awal' => 15,
            'saldo_tersedia' => 10,
        ]);
    }

    public function test_form_menampilkan_satu_jatah_cuti_tanpa_tahun(): void
    {
        $adminHr = $this->buatPegawai(PeranPengguna::AdminHr);
        $staff = $this->buatPegawai(PeranPengguna::Karyawan);
        $staff->update(['jatah_cuti' => 15]);

        $this->travelTo(now()->addYear());

        $this->actingAs($adminHr->pengguna)
            ->get(route('admin_hr.pegawai.edit', $staff))
            ->assertOk()
            ->assertSee('Jatah cuti')
            ->assertDontSee('Jatah cuti '.now()->year)
            ->assertSee('value="15"', false);
    }

    public function test_pengguna_non_hr_dilarang_mengelola_master_karyawan(): void
    {
        $staff = $this->buatPegawai(PeranPengguna::Karyawan);

        $this->actingAs($staff->pengguna)
            ->get(route('admin_hr.pegawai.index'))
            ->assertForbidden();
    }

    private function buatPegawai(PeranPengguna $peran, ?Pegawai $atasan = null): Pegawai
    {
        $pengguna = Pengguna::factory()->create(['peran' => $peran]);

        return Pegawai::factory()
            ->for($pengguna, 'pengguna')
            ->create(['atasan_id' => $atasan?->getKey()]);
    }

    /**
     * @param  array<string, mixed>  $perubahan
     * @return array<string, mixed>
     */
    private function dataKaryawan(BagianOrganisasi $bagian, array $perubahan = []): array
    {
        return [
            'nomor_induk' => 'STF-001',
            'nama' => 'Staff Contoh',
            'email' => 'staff@example.test',
            'kata_sandi' => 'kata-sandi-awal',
            'kata_sandi_confirmation' => 'kata-sandi-awal',
            'bagian_organisasi_id' => $bagian->getKey(),
            'peran' => PeranPengguna::Karyawan->value,
            'atasan_id' => null,
            'jatah_cuti' => '12',
            'tanggal_masuk' => '2026-01-02',
            'aktif' => '1',
            ...$perubahan,
        ];
    }
}
