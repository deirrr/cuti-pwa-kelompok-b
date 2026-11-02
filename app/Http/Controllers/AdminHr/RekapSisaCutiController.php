<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\PeranPengguna;
use App\Enums\StatusPengajuanCuti;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\FilterRekapSisaCutiRequest;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Illuminate\Contracts\View\View;

class RekapSisaCutiController extends Controller
{
    public function __invoke(FilterRekapSisaCutiRequest $request): View
    {
        $data = $request->validated();
        $tanggalMulai = $request->tanggalMulai();
        $tanggalSelesai = $request->tanggalSelesai();
        $tahun = $tanggalMulai->year;
        $pencarian = $data['cari'] ?? '';
        $statusPegawai = $data['status_pegawai'];

        $pegawai = Pegawai::query()
            ->select(['id', 'pengguna_id', 'bagian_organisasi_id', 'nomor_induk', 'nama', 'aktif'])
            ->with([
                'bagianOrganisasi:id,nama',
                'saldoCuti' => fn ($query) => $query
                    ->select(['id', 'pegawai_id', 'tahun', 'jatah_awal', 'saldo_tersedia'])
                    ->where('tahun', $tahun),
            ])
            ->withCount([
                'pengajuanCuti as cuti_disetujui_periode_count' => fn ($query) => $query
                    ->where('status', StatusPengajuanCuti::Disetujui)
                    ->whereBetween('tanggal_cuti', [$tanggalMulai->toDateString(), $tanggalSelesai->toDateString()]),
                'pengajuanCuti as cuti_disetujui_tahun_count' => fn ($query) => $query
                    ->where('status', StatusPengajuanCuti::Disetujui)
                    ->whereYear('tanggal_cuti', $tahun),
                'pengajuanCuti as cuti_menunggu_periode_count' => fn ($query) => $query
                    ->whereIn('status', [
                        StatusPengajuanCuti::MenungguAtasan->value,
                        StatusPengajuanCuti::MenungguHr->value,
                    ])
                    ->whereBetween('tanggal_cuti', [$tanggalMulai->toDateString(), $tanggalSelesai->toDateString()]),
            ])
            ->whereHas('pengguna', fn ($query) => $query->whereIn('peran', [
                PeranPengguna::Karyawan->value,
                PeranPengguna::Atasan->value,
            ]))
            ->when(
                $data['bagian_organisasi_id'] ?? null,
                fn ($query, $bagianOrganisasiId) => $query->where('bagian_organisasi_id', $bagianOrganisasiId),
            )
            ->when(
                $statusPegawai !== 'semua',
                fn ($query) => $query->where('aktif', $statusPegawai === 'aktif'),
            )
            ->when($pencarian !== '', function ($query) use ($pencarian): void {
                $query->where(function ($query) use ($pencarian): void {
                    $query->whereLike('nama', "%{$pencarian}%")
                        ->orWhereLike('nomor_induk', "%{$pencarian}%");
                });
            })
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->orderBy('id')
            ->get();

        $rekap = $pegawai->map(function (Pegawai $item): array {
            /** @var SaldoCuti|null $saldoCuti */
            $saldoCuti = $item->saldoCuti->first();
            $saldoTersedia = $saldoCuti?->saldo_tersedia;
            $cutiMenungguPeriode = $item->cuti_menunggu_periode_count;

            return [
                'nomor_induk' => $item->nomor_induk,
                'nama' => $item->nama,
                'bagian' => $item->bagianOrganisasi->nama,
                'aktif' => $item->aktif,
                'saldo_diterbitkan' => $saldoCuti !== null,
                'jatah_awal' => $saldoCuti?->jatah_awal,
                'cuti_disetujui_periode' => $item->cuti_disetujui_periode_count,
                'cuti_disetujui_tahun' => $item->cuti_disetujui_tahun_count,
                'cuti_menunggu_periode' => $cutiMenungguPeriode,
                'saldo_tersedia' => $saldoTersedia,
                'saldo_efektif' => $saldoTersedia !== null
                    ? max(0, $saldoTersedia - $cutiMenungguPeriode)
                    : null,
            ];
        });

        return view('admin-hr.rekap.sisa-cuti', [
            'rekap' => $rekap,
            'ringkasan' => [
                'jumlah_karyawan' => $rekap->count(),
                'total_jatah_awal' => $rekap->sum('jatah_awal'),
                'total_cuti_disetujui_periode' => $rekap->sum('cuti_disetujui_periode'),
                'total_saldo_tersedia' => $rekap->sum('saldo_tersedia'),
            ],
            'daftarBagian' => BagianOrganisasi::query()
                ->orderByDesc('aktif')
                ->orderBy('nama')
                ->get(['id', 'nama']),
            'tanggalMulai' => $tanggalMulai->toDateString(),
            'tanggalSelesai' => $tanggalSelesai->toDateString(),
            'bagianOrganisasiId' => $data['bagian_organisasi_id'] ?? null,
            'statusPegawai' => $statusPegawai,
            'pencarian' => $pencarian,
            'tahun' => $tahun,
        ]);
    }
}
