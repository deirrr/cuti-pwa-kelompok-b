<?php

namespace App\Http\Controllers;

use App\Enums\KeputusanPersetujuan;
use App\Enums\StatusPengajuanCuti;
use App\Enums\TahapPersetujuan;
use App\Http\Requests\SimpanKeputusanCutiRequest;
use App\Models\PengajuanCuti;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PersetujuanAtasanController extends Controller
{
    public function index(): View
    {
        return view('persetujuan-atasan.index', [
            'pengajuan' => PengajuanCuti::query()
                ->with(['pegawai.bagianOrganisasi'])
                ->where('atasan_penyetuju_id', auth()->id())
                ->where('status', StatusPengajuanCuti::MenungguAtasan)
                ->oldest('diajukan_pada')
                ->get(),
        ]);
    }

    public function store(SimpanKeputusanCutiRequest $request, PengajuanCuti $pengajuanCuti): RedirectResponse
    {
        DB::transaction(function () use ($request, $pengajuanCuti): void {
            $pengajuan = PengajuanCuti::query()->lockForUpdate()->findOrFail($pengajuanCuti->getKey());

            abort_unless(
                $pengajuan->atasan_penyetuju_id === $request->user()->getKey()
                && $pengajuan->status === StatusPengajuanCuti::MenungguAtasan,
                403,
            );

            $keputusan = KeputusanPersetujuan::from($request->validated('keputusan'));

            $pengajuan->persetujuan()->create([
                'pemberi_keputusan_id' => $request->user()->getKey(),
                'tahap' => TahapPersetujuan::Atasan,
                'keputusan' => $keputusan,
                'catatan' => $request->validated('catatan'),
                'diputuskan_pada' => now(),
            ]);

            $pengajuan->update([
                'status' => $keputusan === KeputusanPersetujuan::Disetujui
                    ? StatusPengajuanCuti::MenungguHr
                    : StatusPengajuanCuti::Ditolak,
            ]);
        });

        return back()->with('sukses', 'Keputusan Atasan berhasil disimpan.');
    }
}
