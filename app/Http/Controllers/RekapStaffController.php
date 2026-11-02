<?php

namespace App\Http\Controllers;

use App\Enums\StatusPengajuanCuti;
use App\Http\Requests\FilterRekapStaffRequest;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\SaldoCuti;
use App\Support\UnduhanXlsx;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapStaffController extends Controller
{
    public function __invoke(FilterRekapStaffRequest $request, UnduhanXlsx $unduhanXlsx): View|StreamedResponse
    {
        $data = $request->validated();
        $tanggalMulai = $request->tanggalMulai();
        $tanggalSelesai = $request->tanggalSelesai();
        $tahun = $tanggalMulai->year;
        $statusDipilih = collect($data['status'])->map(fn (string $status): StatusPengajuanCuti => StatusPengajuanCuti::from($status));
        $pencarian = $data['cari'] ?? '';
        $atasan = $request->user()->pegawai()->firstOrFail();

        $staff = Pegawai::query()
            ->select(['id', 'bagian_organisasi_id', 'nomor_induk', 'nama', 'aktif'])
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
            ->where('atasan_id', $atasan->getKey())
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->orderBy('id')
            ->get();

        $rekapSaldo = $staff->map(function (Pegawai $item): array {
            /** @var SaldoCuti|null $saldoCuti */
            $saldoCuti = $item->saldoCuti->first();
            $saldoTersedia = $saldoCuti?->saldo_tersedia;
            $cutiMenungguPeriode = $item->cuti_menunggu_periode_count;

            return [
                'pegawai_id' => $item->getKey(),
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

        $pengajuan = PengajuanCuti::query()
            ->select(['id', 'nomor_pengajuan', 'pegawai_id', 'status', 'alasan', 'tanggal_cuti', 'diajukan_pada'])
            ->with(['pegawai:id,bagian_organisasi_id,nomor_induk,nama', 'pegawai.bagianOrganisasi:id,nama'])
            ->whereBetween('tanggal_cuti', [$tanggalMulai->toDateString(), $tanggalSelesai->toDateString()])
            ->whereIn('status', $statusDipilih->map->value->all())
            ->whereHas('pegawai', fn ($query) => $query->where('atasan_id', $atasan->getKey()))
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

        $ringkasanStatus = collect(FilterRekapStaffRequest::statusLaporan())
            ->mapWithKeys(fn (StatusPengajuanCuti $status): array => [
                $status->value => $pengajuan->where('status', $status)->count(),
            ]);

        if ($request->string('format')->toString() === 'excel') {
            $periode = $tanggalMulai->translatedFormat('d F Y').' sampai '.$tanggalSelesai->translatedFormat('d F Y');

            return $unduhanXlsx->downloadBanyakSheet(
                "rekap-staff-{$tanggalMulai->toDateString()}-sampai-{$tanggalSelesai->toDateString()}.xlsx",
                [
                    [
                        'nama' => 'Detail Pengajuan',
                        'judul' => 'Detail Pengajuan Cuti Staff',
                        'periode' => $periode,
                        'judul_kolom' => ['Nomor Pengajuan', 'NIK', 'Nama Staff', 'Bagian', 'Tanggal Cuti', 'Status', 'Alasan', 'Diajukan Pada'],
                        'baris' => $pengajuan->map(fn (PengajuanCuti $item): array => [
                            $item->nomor_pengajuan,
                            $item->pegawai->nomor_induk,
                            $item->pegawai->nama,
                            $item->pegawai->bagianOrganisasi->nama,
                            $item->tanggal_cuti,
                            $item->status->label(),
                            $item->alasan,
                            $item->diajukan_pada,
                        ]),
                        'tipe_kolom' => ['text', 'text', 'text', 'text', 'date', 'text', 'text', 'datetime'],
                    ],
                    [
                        'nama' => 'Sisa Cuti Staff',
                        'judul' => "Sisa Cuti Staff Tahun {$tahun}",
                        'periode' => $periode,
                        'judul_kolom' => ['NIK', 'Nama Staff', 'Bagian', 'Status Staff', 'Jatah Awal', 'Terpakai Periode', 'Terpakai Tahun', 'Menunggu Periode', 'Sisa Saat Ini', 'Saldo Efektif'],
                        'baris' => $rekapSaldo->map(fn (array $item): array => [
                            $item['nomor_induk'],
                            $item['nama'],
                            $item['bagian'],
                            $item['aktif'] ? 'Aktif' : 'Nonaktif',
                            $item['jatah_awal'],
                            $item['cuti_disetujui_periode'],
                            $item['cuti_disetujui_tahun'],
                            $item['cuti_menunggu_periode'],
                            $item['saldo_tersedia'],
                            $item['saldo_efektif'],
                        ]),
                        'tipe_kolom' => ['text', 'text', 'text', 'text', 'integer', 'integer', 'integer', 'integer', 'integer', 'integer'],
                    ],
                ],
            );
        }

        return view('rekap-staff.index', [
            'pengajuan' => $pengajuan,
            'rekapSaldo' => $rekapSaldo,
            'ringkasanStatus' => $ringkasanStatus,
            'statusTersedia' => FilterRekapStaffRequest::statusLaporan(),
            'statusDipilih' => $statusDipilih->map->value->all(),
            'pencarian' => $pencarian,
            'tanggalMulai' => $tanggalMulai->toDateString(),
            'tanggalSelesai' => $tanggalSelesai->toDateString(),
            'tahun' => $tahun,
        ]);
    }
}
