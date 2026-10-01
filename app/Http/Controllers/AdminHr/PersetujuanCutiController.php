<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\KeputusanPersetujuan;
use App\Enums\StatusPengajuanCuti;
use App\Enums\TahapPersetujuan;
use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanKeputusanCutiRequest;
use App\Models\PengajuanCuti;
use App\Models\SaldoCuti;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersetujuanCutiController extends Controller
{
    public function index(): View
    {
        return view('admin-hr.persetujuan.index', [
            'pengajuan' => PengajuanCuti::query()
                ->with(['pegawai.bagianOrganisasi', 'pegawai.pengguna', 'persetujuan.pemberiKeputusan.pegawai'])
                ->where('status', StatusPengajuanCuti::MenungguHr)
                ->oldest('diajukan_pada')
                ->get(),
        ]);
    }

    public function store(SimpanKeputusanCutiRequest $request, PengajuanCuti $pengajuanCuti): RedirectResponse
    {
        DB::transaction(function () use ($request, $pengajuanCuti): void {
            $pengajuan = PengajuanCuti::query()
                ->with('pegawai')
                ->lockForUpdate()
                ->findOrFail($pengajuanCuti->getKey());

            abort_unless($pengajuan->status === StatusPengajuanCuti::MenungguHr, 403);

            $keputusan = KeputusanPersetujuan::from($request->validated('keputusan'));

            if ($keputusan === KeputusanPersetujuan::Disetujui) {
                $tahun = $pengajuan->tanggal_cuti->year;
                SaldoCuti::query()->firstOrCreate(
                    ['pegawai_id' => $pengajuan->pegawai_id, 'tahun' => $tahun],
                    [
                        'jatah_awal' => $pengajuan->pegawai->jatah_cuti,
                        'saldo_tersedia' => $pengajuan->pegawai->jatah_cuti,
                    ],
                );
                $saldoCuti = SaldoCuti::query()
                    ->where('pegawai_id', $pengajuan->pegawai_id)
                    ->where('tahun', $tahun)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($saldoCuti->saldo_tersedia < 1) {
                    throw ValidationException::withMessages([
                        'keputusan' => 'Saldo cuti karyawan tidak mencukupi untuk menyetujui pengajuan ini.',
                    ]);
                }

                $saldoCuti->decrement('saldo_tersedia');
            }

            $pengajuan->persetujuan()->create([
                'pemberi_keputusan_id' => $request->user()->getKey(),
                'tahap' => TahapPersetujuan::AdminHr,
                'keputusan' => $keputusan,
                'catatan' => $request->validated('catatan'),
                'diputuskan_pada' => now(),
            ]);

            $pengajuan->update([
                'status' => $keputusan === KeputusanPersetujuan::Disetujui
                    ? StatusPengajuanCuti::Disetujui
                    : StatusPengajuanCuti::Ditolak,
            ]);
        });

        return back()->with('sukses', 'Keputusan Admin HR berhasil disimpan.');
    }
}
