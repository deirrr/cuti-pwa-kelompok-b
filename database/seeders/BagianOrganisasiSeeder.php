<?php

namespace Database\Seeders;

use App\Enums\JenisBagianOrganisasi;
use App\Models\BagianOrganisasi;
use App\Models\UnitBisnis;
use Illuminate\Database\Seeder;

class BagianOrganisasiSeeder extends Seeder
{
    public function run(): void
    {
        $unitBisnis = UnitBisnis::query()
            ->whereIn('kode', ['HO', 'KUMA', 'KPMA', 'APOTEK', 'PMB', 'MEDLAB'])
            ->get()
            ->keyBy('kode');

        $headOffice = $unitBisnis->get('HO');
        $klinikUtama = $unitBisnis->get('KUMA');
        $klinikPratama = $unitBisnis->get('KPMA');
        $apotek = $unitBisnis->get('APOTEK');
        $praktikMandiriBidan = $unitBisnis->get('PMB');
        $medikaLaboratorium = $unitBisnis->get('MEDLAB');

        abort_unless(
            $headOffice && $klinikUtama && $klinikPratama && $apotek && $praktikMandiriBidan && $medikaLaboratorium,
            500,
            'Unit bisnis untuk data awal bagian organisasi belum lengkap.',
        );

        $direktoratPelayanan = $this->simpan(
            $headOffice,
            'DIR-PELAYANAN',
            'Direktorat Pelayanan',
            JenisBagianOrganisasi::Direktorat,
        );
        $direktoratUmum = $this->simpan(
            $headOffice,
            'DIR-UMUM',
            'Direktorat Umum',
            JenisBagianOrganisasi::Direktorat,
        );
        $direktoratKeuangan = $this->simpan(
            $headOffice,
            'DIR-KEUANGAN',
            'Direktorat Keuangan',
            JenisBagianOrganisasi::Direktorat,
        );

        foreach ([
            ['kode' => 'PELAYANAN', 'nama' => 'Pelayanan', 'induk' => $direktoratPelayanan],
            ['kode' => 'PENGADAAN', 'nama' => 'Pengadaan', 'induk' => $direktoratPelayanan],
            ['kode' => 'IT', 'nama' => 'IT', 'induk' => $direktoratUmum],
            ['kode' => 'SDM', 'nama' => 'Sumber Daya Manusia', 'induk' => $direktoratUmum],
            ['kode' => 'UMUM', 'nama' => 'Umum', 'induk' => $direktoratUmum],
            ['kode' => 'CLEANING-SERVICE', 'nama' => 'Cleaning Service', 'induk' => $direktoratUmum],
            ['kode' => 'KEUANGAN', 'nama' => 'Keuangan', 'induk' => $direktoratKeuangan],
            ['kode' => 'MARKETING', 'nama' => 'Marketing', 'induk' => $direktoratKeuangan],
            ['kode' => 'SEKRETARIAT', 'nama' => 'Sekretariat', 'induk' => null],
        ] as $departemen) {
            $this->simpan(
                $headOffice,
                $departemen['kode'],
                $departemen['nama'],
                JenisBagianOrganisasi::Departemen,
                $departemen['induk'],
            );
        }

        $this->simpanSemuaBagian($klinikUtama, [
            ['kode' => 'MANAJEMEN', 'nama' => 'Manajemen'],
            ['kode' => 'PENDAFTARAN', 'nama' => 'Pendaftaran'],
            ['kode' => 'SATPAM', 'nama' => 'Satpam'],
            ['kode' => 'UMUM', 'nama' => 'Umum'],
            ['kode' => 'CLEANING-SERVICE', 'nama' => 'Cleaning Service'],
            ['kode' => 'PERAWAT-GIGI', 'nama' => 'Perawat Gigi'],
            ['kode' => 'ADMIN-JKN', 'nama' => 'Admin JKN'],
            ['kode' => 'PERAWAT-UMUM', 'nama' => 'Perawat Umum'],
            ['kode' => 'INSTALASI-FARMASI', 'nama' => 'Instalasi Farmasi'],
            ['kode' => 'AMBULATUR', 'nama' => 'Ambulatur'],
            ['kode' => 'KASIR', 'nama' => 'Kasir'],
            ['kode' => 'ADMIN', 'nama' => 'Admin'],
            ['kode' => 'RADIOGRAFER', 'nama' => 'Radiografer'],
            ['kode' => 'PELAYANAN', 'nama' => 'Pelayanan'],
            ['kode' => 'DOKTER', 'nama' => 'Dokter'],
            ['kode' => 'ASISTEN-OBGYN', 'nama' => 'Asisten Obgyn'],
            ['kode' => 'KESMAS', 'nama' => 'Kesehatan Masyarakat'],
            ['kode' => 'TVF', 'nama' => 'Tenaga Vokasi Farmasi'],
            ['kode' => 'APOTEKER', 'nama' => 'Apoteker'],
        ]);

        $this->simpanSemuaBagian($klinikPratama, [
            ['kode' => 'MANAJEMEN', 'nama' => 'Manajemen'],
            ['kode' => 'PENDAFTARAN', 'nama' => 'Pendaftaran'],
            ['kode' => 'PERAWAT-GIGI', 'nama' => 'Perawat Gigi'],
            ['kode' => 'PERAWAT-UMUM', 'nama' => 'Perawat Umum'],
            ['kode' => 'SATPAM', 'nama' => 'Satpam'],
            ['kode' => 'AMBULATUR', 'nama' => 'Ambulatur'],
            ['kode' => 'CUSTOMER-SERVICE', 'nama' => 'Customer Service'],
            ['kode' => 'TTK', 'nama' => 'Tenaga Teknis Kefarmasian'],
            ['kode' => 'KASIR', 'nama' => 'Kasir'],
            ['kode' => 'UMUM', 'nama' => 'Umum'],
            ['kode' => 'STERILISASI', 'nama' => 'Sterilisasi'],
            ['kode' => 'ADMIN', 'nama' => 'Admin'],
            ['kode' => 'KESMAS', 'nama' => 'Kesehatan Masyarakat'],
            ['kode' => 'REFRAKSI-OPTISI', 'nama' => 'Refraksi Optisi'],
            ['kode' => 'PELAYANAN', 'nama' => 'Pelayanan'],
            ['kode' => 'DOKTER', 'nama' => 'Dokter'],
            ['kode' => 'FISIOTERAPI', 'nama' => 'Fisioterapi'],
            ['kode' => 'TVF', 'nama' => 'Tenaga Vokasi Farmasi'],
            ['kode' => 'APOTEKER', 'nama' => 'Apoteker'],
        ]);

        $this->simpanSemuaBagian($apotek, [
            ['kode' => 'APOTEKER', 'nama' => 'Apoteker'],
            ['kode' => 'TVF', 'nama' => 'Tenaga Vokasi Farmasi'],
        ]);

        $this->simpanSemuaBagian($praktikMandiriBidan, [
            ['kode' => 'ADMIN', 'nama' => 'Administrasi'],
            ['kode' => 'PELAYANAN-KEBIDANAN', 'nama' => 'Pelayanan Kebidanan'],
        ]);

        $this->simpanSemuaBagian($medikaLaboratorium, [
            ['kode' => 'MANAJEMEN-LAB', 'nama' => 'Manajemen Laboratorium'],
            ['kode' => 'LABORATORIUM', 'nama' => 'Laboratorium'],
            ['kode' => 'PELAYANAN', 'nama' => 'Pelayanan'],
        ]);

        BagianOrganisasi::query()
            ->whereBelongsTo($klinikUtama, 'unitBisnis')
            ->where('kode', 'OPS')
            ->update([
                'jenis' => JenisBagianOrganisasi::Bagian,
                'nama' => 'Operasional (data lama)',
                'aktif' => false,
            ]);
    }

    /**
     * @param  array<int, array{kode: string, nama: string}>  $daftarBagian
     */
    private function simpanSemuaBagian(UnitBisnis $unitBisnis, array $daftarBagian): void
    {
        foreach ($daftarBagian as $bagian) {
            $this->simpan(
                $unitBisnis,
                $bagian['kode'],
                $bagian['nama'],
                JenisBagianOrganisasi::Bagian,
            );
        }
    }

    private function simpan(
        UnitBisnis $unitBisnis,
        string $kode,
        string $nama,
        JenisBagianOrganisasi $jenis,
        ?BagianOrganisasi $induk = null,
    ): BagianOrganisasi {
        return BagianOrganisasi::query()->updateOrCreate(
            ['unit_bisnis_id' => $unitBisnis->getKey(), 'kode' => $kode],
            [
                'induk_id' => $induk?->getKey(),
                'jenis' => $jenis,
                'nama' => $nama,
                'aktif' => true,
            ],
        );
    }
}
