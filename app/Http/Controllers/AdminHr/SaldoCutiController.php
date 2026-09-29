<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\PerbaruiSaldoCutiRequest;
use App\Models\Pegawai;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SaldoCutiController extends Controller
{
    public function index(Request $request): View
    {
        $tahun = $request->integer('tahun', now()->year);
        $pencarian = $request->string('cari')->trim()->toString();

        $pegawai = Pegawai::query()
            ->with(['bagianOrganisasi', 'saldoCuti' => fn ($query) => $query->where('tahun', $tahun)])
            ->whereHas('pengguna', fn ($query) => $query->whereIn('peran', [
                PeranPengguna::Karyawan->value,
                PeranPengguna::Atasan->value,
            ]))
            ->when($pencarian !== '', function ($query) use ($pencarian): void {
                $query->where(function ($query) use ($pencarian): void {
                    $query->where('nama', 'like', "%{$pencarian}%")
                        ->orWhere('nomor_induk', 'like', "%{$pencarian}%");
                });
            })
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin-hr.saldo-cuti.index', compact('pegawai', 'tahun', 'pencarian'));
    }

    public function edit(Pegawai $pegawai, Request $request): View
    {
        abort_unless(in_array($pegawai->pengguna?->peran, [PeranPengguna::Karyawan, PeranPengguna::Atasan], true), 404);

        $tahun = $request->integer('tahun', now()->year);
        $saldoCuti = $pegawai->saldoCuti()->firstOrNew(
            ['tahun' => $tahun],
            ['jatah_awal' => 12, 'saldo_tersedia' => 12],
        );

        return view('admin-hr.saldo-cuti.edit', compact('pegawai', 'saldoCuti', 'tahun'));
    }

    public function update(PerbaruiSaldoCutiRequest $request, Pegawai $pegawai): RedirectResponse
    {
        $data = $request->validated();

        $pegawai->saldoCuti()->updateOrCreate(
            ['tahun' => $data['tahun']],
            [
                'jatah_awal' => $data['jatah_awal'],
                'saldo_tersedia' => $data['saldo_tersedia'],
                'catatan' => $data['catatan'] ?? null,
            ],
        );

        return redirect()
            ->route('admin_hr.saldo_cuti.index', ['tahun' => $data['tahun']])
            ->with('sukses', "Jatah cuti tahunan {$pegawai->nama} berhasil diperbarui.");
    }
}
