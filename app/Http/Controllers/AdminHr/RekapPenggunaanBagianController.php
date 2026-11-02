<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\PeranPengguna;
use App\Enums\StatusPengajuanCuti;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\FilterRekapPenggunaanBagianRequest;
use App\Models\BagianOrganisasi;
use App\Models\PengajuanCuti;
use App\Support\UnduhanXlsx;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapPenggunaanBagianController extends Controller
{
    public function __invoke(FilterRekapPenggunaanBagianRequest $request, UnduhanXlsx $unduhanXlsx): View|StreamedResponse
    {
        $data = $request->validated();
        $tanggalMulai = $request->tanggalMulai();
        $tanggalSelesai = $request->tanggalSelesai();
        $bagianOrganisasiId = $data['bagian_organisasi_id'] ?? null;
        $peranPegawai = [PeranPengguna::Karyawan->value, PeranPengguna::Atasan->value];

        $daftarBagian = BagianOrganisasi::query()
            ->withCount(['pegawai as jumlah_pegawai_aktif' => fn ($query) => $query
                ->where('aktif', true)
                ->whereHas('pengguna', fn ($query) => $query->whereIn('peran', $peranPegawai))])
            ->when($bagianOrganisasiId, fn ($query, $bagianId) => $query->whereKey($bagianId))
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'aktif']);

        $pengajuan = PengajuanCuti::query()
            ->select(['id', 'pegawai_id', 'tanggal_cuti'])
            ->with('pegawai:id,bagian_organisasi_id')
            ->where('status', StatusPengajuanCuti::Disetujui)
            ->whereBetween('tanggal_cuti', [$tanggalMulai->toDateString(), $tanggalSelesai->toDateString()])
            ->whereHas('pegawai.pengguna', fn ($query) => $query->whereIn('peran', $peranPegawai))
            ->when($bagianOrganisasiId, fn ($query, $bagianId) => $query->whereHas('pegawai', fn ($query) => $query->where('bagian_organisasi_id', $bagianId)))
            ->get();

        $pengajuanPerBagian = $pengajuan->groupBy('pegawai.bagian_organisasi_id');
        $rekap = $daftarBagian->map(function (BagianOrganisasi $bagian) use ($pengajuanPerBagian): array {
            $cuti = $pengajuanPerBagian->get($bagian->getKey(), collect());
            $jumlahPegawaiCuti = $cuti->unique('pegawai_id')->count();

            return [
                'id' => $bagian->getKey(),
                'nama' => $bagian->nama,
                'aktif' => $bagian->aktif,
                'jumlah_pegawai_aktif' => $bagian->jumlah_pegawai_aktif,
                'jumlah_pegawai_cuti' => $jumlahPegawaiCuti,
                'jumlah_hari_cuti' => $cuti->count(),
                'rata_rata_hari' => $jumlahPegawaiCuti > 0 ? round($cuti->count() / $jumlahPegawaiCuti, 1) : 0,
            ];
        });

        $jumlahPegawaiCuti = $pengajuan->unique('pegawai_id')->count();

        if ($request->string('format')->toString() === 'excel') {
            return $unduhanXlsx->download(
                "rekap-penggunaan-bagian-{$tanggalMulai->toDateString()}-sampai-{$tanggalSelesai->toDateString()}.xlsx",
                'Rekap Penggunaan Cuti per Bagian',
                $tanggalMulai->translatedFormat('d F Y').' sampai '.$tanggalSelesai->translatedFormat('d F Y'),
                ['Bagian', 'Status Bagian', 'Pegawai Aktif', 'Pegawai Mengambil Cuti', 'Hari Cuti Disetujui', 'Rata-rata Hari per Karyawan'],
                $rekap->map(fn (array $item): array => [
                    $item['nama'],
                    $item['aktif'] ? 'Aktif' : 'Nonaktif',
                    $item['jumlah_pegawai_aktif'],
                    $item['jumlah_pegawai_cuti'],
                    $item['jumlah_hari_cuti'],
                    $item['rata_rata_hari'],
                ]),
                ['text', 'text', 'integer', 'integer', 'integer', 'decimal'],
            );
        }

        return view('admin-hr.rekap.penggunaan-bagian', [
            'rekap' => $rekap,
            'ringkasan' => [
                'jumlah_bagian' => $rekap->count(),
                'jumlah_pegawai_cuti' => $jumlahPegawaiCuti,
                'jumlah_hari_cuti' => $pengajuan->count(),
                'rata_rata_hari' => $jumlahPegawaiCuti > 0 ? round($pengajuan->count() / $jumlahPegawaiCuti, 1) : 0,
            ],
            'daftarBagian' => BagianOrganisasi::query()->orderByDesc('aktif')->orderBy('nama')->get(['id', 'nama']),
            'bagianOrganisasiId' => $bagianOrganisasiId,
            'tanggalMulai' => $tanggalMulai->toDateString(),
            'tanggalSelesai' => $tanggalSelesai->toDateString(),
        ]);
    }
}
