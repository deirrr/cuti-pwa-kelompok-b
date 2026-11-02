<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\PeranPengguna;
use App\Enums\StatusPengajuanCuti;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\FilterRekapStatusCutiRequest;
use App\Http\Requests\AdminHr\FilterRekapTrenBulananRequest;
use App\Models\BagianOrganisasi;
use App\Models\PengajuanCuti;
use App\Support\UnduhanXlsx;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapTrenBulananController extends Controller
{
    public function __invoke(FilterRekapTrenBulananRequest $request, UnduhanXlsx $unduhanXlsx): View|StreamedResponse
    {
        $data = $request->validated();
        $tanggalMulai = $request->tanggalMulai();
        $tanggalSelesai = $request->tanggalSelesai();
        $statusDipilih = collect($data['status'])->map(fn (string $status): StatusPengajuanCuti => StatusPengajuanCuti::from($status));

        $pengajuan = PengajuanCuti::query()
            ->select(['id', 'pegawai_id', 'status', 'tanggal_cuti'])
            ->whereBetween('tanggal_cuti', [$tanggalMulai->toDateString(), $tanggalSelesai->toDateString()])
            ->whereIn('status', $statusDipilih->map->value->all())
            ->whereHas('pegawai.pengguna', fn ($query) => $query->whereIn('peran', [PeranPengguna::Karyawan->value, PeranPengguna::Atasan->value]))
            ->when($data['bagian_organisasi_id'] ?? null, fn ($query, $bagianId) => $query->whereHas('pegawai', fn ($query) => $query->where('bagian_organisasi_id', $bagianId)))
            ->get();

        $pengajuanPerBulan = $pengajuan->groupBy(fn (PengajuanCuti $item): string => $item->tanggal_cuti->format('Y-m'));
        $rekap = collect();
        $bulan = $tanggalMulai->startOfMonth();
        $bulanTerakhir = $tanggalSelesai->startOfMonth();

        while ($bulan->lessThanOrEqualTo($bulanTerakhir)) {
            $dataBulan = $pengajuanPerBulan->get($bulan->format('Y-m'), collect());
            $jumlahPerStatus = $statusDipilih->mapWithKeys(fn (StatusPengajuanCuti $status): array => [
                $status->value => $dataBulan->where('status', $status)->count(),
            ]);

            $rekap->push([
                'kunci' => $bulan->format('Y-m'),
                'label' => CarbonImmutable::parse($bulan)->translatedFormat('M Y'),
                'jumlah_per_status' => $jumlahPerStatus,
                'total' => $dataBulan->count(),
            ]);

            $bulan = $bulan->addMonth();
        }

        $bulanTersibuk = $rekap->sortByDesc('total')->first();

        if ($request->string('format')->toString() === 'excel') {
            return $unduhanXlsx->download(
                "rekap-tren-bulanan-{$tanggalMulai->toDateString()}-sampai-{$tanggalSelesai->toDateString()}.xlsx",
                'Rekap Tren Cuti Bulanan',
                $tanggalMulai->translatedFormat('d F Y').' sampai '.$tanggalSelesai->translatedFormat('d F Y'),
                ['Bulan', ...$statusDipilih->map->label()->all(), 'Total'],
                $rekap->map(fn (array $item): array => [
                    $item['label'],
                    ...$statusDipilih->map(fn (StatusPengajuanCuti $status): int => $item['jumlah_per_status'][$status->value])->all(),
                    $item['total'],
                ]),
                ['text', ...array_fill(0, $statusDipilih->count() + 1, 'integer')],
            );
        }

        return view('admin-hr.rekap.tren-bulanan', [
            'rekap' => $rekap,
            'ringkasan' => [
                'jumlah_bulan' => $rekap->count(),
                'jumlah_cuti' => $pengajuan->count(),
                'bulan_tersibuk' => ($bulanTersibuk['total'] ?? 0) > 0 ? $bulanTersibuk['label'] : '-',
                'jumlah_tersibuk' => $bulanTersibuk['total'] ?? 0,
            ],
            'nilaiMaksimum' => max(1, (int) $rekap->max('total')),
            'statusTersedia' => FilterRekapStatusCutiRequest::statusLaporan(),
            'statusDipilih' => $statusDipilih->map->value->all(),
            'daftarBagian' => BagianOrganisasi::query()->orderByDesc('aktif')->orderBy('nama')->get(['id', 'nama']),
            'bagianOrganisasiId' => $data['bagian_organisasi_id'] ?? null,
            'tanggalMulai' => $tanggalMulai->toDateString(),
            'tanggalSelesai' => $tanggalSelesai->toDateString(),
        ]);
    }
}
