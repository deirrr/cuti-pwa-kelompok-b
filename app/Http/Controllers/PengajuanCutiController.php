<?php

namespace App\Http\Controllers;

use App\Enums\JenisHariLibur;
use App\Enums\PeranPengguna;
use App\Enums\StatusPengajuanCuti;
use App\Http\Requests\SimpanPengajuanCutiRequest;
use App\Models\HariLibur;
use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\SaldoCuti;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PengajuanCutiController extends Controller
{
    public function index(Request $request): View
    {
        $pegawai = auth()->user()->pegawai;
        $ringkasanPengajuan = $pegawai->pengajuanCuti()
            ->get(['tanggal_cuti', 'status']);
        $tahunTersedia = $ringkasanPengajuan
            ->map(fn (PengajuanCuti $pengajuan): int => $pengajuan->tanggal_cuti->year)
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();
        $tahun = $request->integer('tahun', now()->year);

        if (! $tahunTersedia->contains($tahun)) {
            $tahun = now()->year;
        }

        $statusYangDapatDipilih = [
            StatusPengajuanCuti::MenungguAtasan,
            StatusPengajuanCuti::MenungguHr,
            StatusPengajuanCuti::Dibatalkan,
            StatusPengajuanCuti::Ditolak,
            StatusPengajuanCuti::Disetujui,
        ];
        $statusDiminta = $request->query('status');
        $statusTerpilih = is_string($statusDiminta)
            && in_array($statusDiminta, array_map(fn (StatusPengajuanCuti $status): string => $status->value, $statusYangDapatDipilih), true)
                ? $statusDiminta
                : null;
        $jumlahPerStatus = $ringkasanPengajuan
            ->filter(fn (PengajuanCuti $pengajuan): bool => $pengajuan->tanggal_cuti->year === $tahun)
            ->countBy(fn (PengajuanCuti $pengajuan): string => $pengajuan->status->value);
        $saldoCuti = $pegawai->saldoCuti()->where('tahun', $tahun)->first();

        return view('cuti.index', [
            'pengajuan' => $pegawai->pengajuanCuti()
                ->with(['persetujuan.pemberiKeputusan.pegawai'])
                ->whereYear('tanggal_cuti', $tahun)
                ->when($statusTerpilih !== null, fn ($query) => $query->where('status', $statusTerpilih))
                ->latest('diajukan_pada')
                ->latest('id')
                ->get(),
            'saldoTersedia' => $saldoCuti?->saldo_tersedia ?? $pegawai->jatah_cuti,
            'statusTerpilih' => $statusTerpilih,
            'tahun' => $tahun,
            'tahunTersedia' => $tahunTersedia,
            'pilihanStatus' => collect($statusYangDapatDipilih)->map(fn (StatusPengajuanCuti $status): array => [
                'nilai' => $status->value,
                'label' => match ($status) {
                    StatusPengajuanCuti::Dibatalkan => 'Batal',
                    StatusPengajuanCuti::Disetujui => 'ACC',
                    default => $status->label(),
                },
                'jumlah' => $jumlahPerStatus->get($status->value, 0),
            ]),
        ]);
    }

    public function create(): View
    {
        $pegawai = auth()->user()->pegawai;

        return view('cuti.create', [
            'saldoTersedia' => $this->saldoDapatDiajukan($pegawai, now()->year),
            'hariLiburNasional' => $this->hariLiburNasional(),
            'tanggalTerpilih' => old('tanggal_cuti', []),
        ]);
    }

    public function store(SimpanPengajuanCutiRequest $request): RedirectResponse
    {
        $tanggalCuti = $request->tanggalCuti();

        DB::transaction(function () use ($request, $tanggalCuti): void {
            /** @var Pegawai $pegawai */
            $pegawai = Pegawai::query()
                ->with(['pengguna', 'atasan.pengguna'])
                ->lockForUpdate()
                ->findOrFail($request->user()->pegawai->getKey());

            $this->pastikanTanggalTersedia($pegawai, $tanggalCuti);

            $status = $pegawai->pengguna->peran === PeranPengguna::Karyawan
                ? StatusPengajuanCuti::MenungguAtasan
                : StatusPengajuanCuti::MenungguHr;
            $atasanPenyetujuId = $status === StatusPengajuanCuti::MenungguAtasan
                ? $pegawai->atasan?->pengguna_id
                : null;

            if ($status === StatusPengajuanCuti::MenungguAtasan && $atasanPenyetujuId === null) {
                throw ValidationException::withMessages([
                    'tanggal_cuti' => 'Atasan langsung belum tersedia. Hubungi Admin HR.',
                ]);
            }

            foreach ($tanggalCuti as $tanggal) {
                $tahun = CarbonImmutable::parse($tanggal)->year;
                $this->saldoCuti($pegawai, $tahun);

                PengajuanCuti::query()->create([
                    'nomor_pengajuan' => 'CUTI-'.$tahun.'-'.Str::upper(Str::random(10)),
                    'pegawai_id' => $pegawai->getKey(),
                    'atasan_penyetuju_id' => $atasanPenyetujuId,
                    'status' => $status,
                    'alasan' => $request->validated('alasan'),
                    'tanggal_cuti' => $tanggal,
                    'diajukan_pada' => now(),
                ]);
            }
        }, attempts: 3);

        return redirect()
            ->route('cuti.index')
            ->with('sukses', $tanggalCuti->count().' pengajuan cuti berhasil dikirim.');
    }

    public function edit(PengajuanCuti $pengajuanCuti): View
    {
        $this->pastikanDapatDiubah($pengajuanCuti, auth()->user()->pegawai->getKey());

        return view('cuti.edit', [
            'pengajuanCuti' => $pengajuanCuti,
            'hariLiburNasional' => $this->hariLiburNasional(),
            'tanggalTerpilih' => old('tanggal_cuti', [$pengajuanCuti->tanggal_cuti->toDateString()]),
        ]);
    }

    public function update(
        SimpanPengajuanCutiRequest $request,
        PengajuanCuti $pengajuanCuti,
    ): RedirectResponse {
        $tanggalCuti = $request->tanggalCuti();

        DB::transaction(function () use ($request, $pengajuanCuti, $tanggalCuti): void {
            $pengajuan = PengajuanCuti::query()->lockForUpdate()->findOrFail($pengajuanCuti->getKey());
            $this->pastikanDapatDiubah($pengajuan, $request->user()->pegawai->getKey());

            $pegawai = Pegawai::query()->lockForUpdate()->findOrFail($pengajuan->pegawai_id);
            $this->pastikanTanggalTersedia($pegawai, $tanggalCuti, $pengajuan);
            $this->saldoCuti($pegawai, CarbonImmutable::parse($tanggalCuti->first())->year);

            $pengajuan->update([
                'tanggal_cuti' => $tanggalCuti->first(),
                'alasan' => $request->validated('alasan'),
            ]);
        }, attempts: 3);

        return redirect()
            ->route('cuti.index')
            ->with('sukses', 'Pengajuan cuti berhasil diperbarui.');
    }

    public function cancel(Request $request, PengajuanCuti $pengajuanCuti): RedirectResponse
    {
        DB::transaction(function () use ($request, $pengajuanCuti): void {
            $pengajuan = PengajuanCuti::query()->lockForUpdate()->findOrFail($pengajuanCuti->getKey());
            $this->pastikanDapatDiubah($pengajuan, $request->user()->pegawai->getKey());
            $pengajuan->update([
                'status' => StatusPengajuanCuti::Dibatalkan,
                'dibatalkan_pada' => now(),
            ]);
        });

        return back()->with('sukses', 'Pengajuan cuti berhasil dibatalkan.');
    }

    /** @return Collection<int, string> */
    private function hariLiburNasional(): Collection
    {
        return HariLibur::query()
            ->where('aktif', true)
            ->where('jenis', JenisHariLibur::Nasional->value)
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->get()
            ->map(fn (HariLibur $hariLibur): string => $hariLibur->tanggal->toDateString());
    }

    private function saldoDapatDiajukan(
        Pegawai $pegawai,
        int $tahun,
        ?PengajuanCuti $pengajuanSaatIni = null,
    ): int {
        $saldoTersedia = $pegawai->saldoCuti()->where('tahun', $tahun)->value('saldo_tersedia')
            ?? $pegawai->jatah_cuti;
        $cutiMenunggu = $pegawai->pengajuanCuti()
            ->when($pengajuanSaatIni !== null, fn ($query) => $query->whereKeyNot($pengajuanSaatIni->getKey()))
            ->whereIn('status', [
                StatusPengajuanCuti::MenungguAtasan->value,
                StatusPengajuanCuti::MenungguHr->value,
            ])
            ->whereYear('tanggal_cuti', $tahun)
            ->count();

        return max(0, $saldoTersedia - $cutiMenunggu);
    }

    /** @param Collection<int, string> $tanggalCuti */
    private function pastikanTanggalTersedia(
        Pegawai $pegawai,
        Collection $tanggalCuti,
        ?PengajuanCuti $pengajuanSaatIni = null,
    ): void {
        $bertumpuk = PengajuanCuti::query()
            ->whereBelongsTo($pegawai)
            ->when($pengajuanSaatIni !== null, fn ($query) => $query->whereKeyNot($pengajuanSaatIni->getKey()))
            ->whereIn('status', [
                StatusPengajuanCuti::MenungguAtasan->value,
                StatusPengajuanCuti::MenungguHr->value,
                StatusPengajuanCuti::Disetujui->value,
            ])
            ->whereIn('tanggal_cuti', $tanggalCuti)
            ->exists();

        if ($bertumpuk) {
            throw ValidationException::withMessages([
                'tanggal_cuti' => 'Salah satu tanggal sudah memiliki pengajuan cuti aktif.',
            ]);
        }

        foreach ($tanggalCuti->groupBy(fn (string $tanggal): int => CarbonImmutable::parse($tanggal)->year) as $tahun => $tanggalPerTahun) {
            if ($tanggalPerTahun->count() > $this->saldoDapatDiajukan($pegawai, $tahun, $pengajuanSaatIni)) {
                throw ValidationException::withMessages([
                    'tanggal_cuti' => "Jumlah tanggal cuti tahun {$tahun} melebihi saldo yang tersedia.",
                ]);
            }
        }
    }

    private function saldoCuti(Pegawai $pegawai, int $tahun): SaldoCuti
    {
        SaldoCuti::query()->firstOrCreate(
            ['pegawai_id' => $pegawai->getKey(), 'tahun' => $tahun],
            ['jatah_awal' => $pegawai->jatah_cuti, 'saldo_tersedia' => $pegawai->jatah_cuti],
        );

        return SaldoCuti::query()
            ->whereBelongsTo($pegawai)
            ->where('tahun', $tahun)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function pastikanDapatDiubah(PengajuanCuti $pengajuan, int $pegawaiId): void
    {
        abort_unless(
            $pengajuan->pegawai_id === $pegawaiId
            && in_array($pengajuan->status, [
                StatusPengajuanCuti::MenungguAtasan,
                StatusPengajuanCuti::MenungguHr,
            ], true)
            && ! $pengajuan->persetujuan()->exists(),
            403,
        );
    }
}
