<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\PeranPengguna;
use App\Enums\StatusPengajuanCuti;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\FilterRekapStatusCutiRequest;
use App\Models\BagianOrganisasi;
use App\Models\PengajuanCuti;
use App\Support\UnduhanXlsx;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapStatusCutiController extends Controller
{
    public function __invoke(FilterRekapStatusCutiRequest $request, UnduhanXlsx $unduhanXlsx): View|StreamedResponse
    {
        $data = $request->validated();
        $tanggalMulai = $request->tanggalMulai();
        $tanggalSelesai = $request->tanggalSelesai();
        $statusDipilih = collect($data['status'])->map(fn (string $status): StatusPengajuanCuti => StatusPengajuanCuti::from($status));
        $pencarian = $data['cari'] ?? '';

        $pengajuan = PengajuanCuti::query()
            ->select(['id', 'nomor_pengajuan', 'pegawai_id', 'status', 'tanggal_cuti', 'diajukan_pada'])
            ->with(['pegawai:id,bagian_organisasi_id,nomor_induk,nama', 'pegawai.bagianOrganisasi:id,nama'])
            ->whereBetween('tanggal_cuti', [$tanggalMulai->toDateString(), $tanggalSelesai->toDateString()])
            ->whereIn('status', $statusDipilih->map->value->all())
            ->whereHas('pegawai.pengguna', fn ($query) => $query->whereIn('peran', [PeranPengguna::Karyawan->value, PeranPengguna::Atasan->value]))
            ->when($data['bagian_organisasi_id'] ?? null, fn ($query, $bagianId) => $query->whereHas('pegawai', fn ($query) => $query->where('bagian_organisasi_id', $bagianId)))
            ->when($pencarian !== '', function ($query) use ($pencarian): void {
                $query->where(function ($query) use ($pencarian): void {
                    $query->whereLike('nomor_pengajuan', "%{$pencarian}%")
                        ->orWhereHas('pegawai', fn ($query) => $query
                            ->whereLike('nama', "%{$pencarian}%")
                            ->orWhereLike('nomor_induk', "%{$pencarian}%"));
                });
            })
            ->orderByDesc('tanggal_cuti')
            ->orderByDesc('id')
            ->get();

        $ringkasanStatus = collect(FilterRekapStatusCutiRequest::statusLaporan())
            ->mapWithKeys(fn (StatusPengajuanCuti $status): array => [
                $status->value => $pengajuan->where('status', $status)->count(),
            ]);

        if ($request->string('format')->toString() === 'excel') {
            return $unduhanXlsx->download(
                "rekap-status-cuti-{$tanggalMulai->toDateString()}-sampai-{$tanggalSelesai->toDateString()}.xlsx",
                'Rekap Cuti Berdasarkan Status',
                $tanggalMulai->translatedFormat('d F Y').' sampai '.$tanggalSelesai->translatedFormat('d F Y'),
                ['Nomor Pengajuan', 'NIK', 'Nama', 'Bagian', 'Tanggal Cuti', 'Status', 'Diajukan Pada'],
                $pengajuan->map(fn (PengajuanCuti $item): array => [
                    $item->nomor_pengajuan,
                    $item->pegawai->nomor_induk,
                    $item->pegawai->nama,
                    $item->pegawai->bagianOrganisasi->nama,
                    $item->tanggal_cuti,
                    $item->status->label(),
                    $item->diajukan_pada,
                ]),
                ['text', 'text', 'text', 'text', 'date', 'text', 'datetime'],
            );
        }

        return view('admin-hr.rekap.status-cuti', [
            'pengajuan' => $pengajuan,
            'ringkasanStatus' => $ringkasanStatus,
            'statusTersedia' => FilterRekapStatusCutiRequest::statusLaporan(),
            'statusDipilih' => $statusDipilih->map->value->all(),
            'daftarBagian' => BagianOrganisasi::query()->orderByDesc('aktif')->orderBy('nama')->get(['id', 'nama']),
            'bagianOrganisasiId' => $data['bagian_organisasi_id'] ?? null,
            'pencarian' => $pencarian,
            'tanggalMulai' => $tanggalMulai->toDateString(),
            'tanggalSelesai' => $tanggalSelesai->toDateString(),
        ]);
    }
}
