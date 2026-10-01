<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\JenisHariLibur;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\PerbaruiHariLiburRequest;
use App\Http\Requests\AdminHr\SimpanHariLiburRequest;
use App\Models\HariLibur;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index(Request $request): View
    {
        $tahun = $request->integer('tahun', now()->year);

        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = now()->year;
        }

        $hariLibur = HariLibur::query()
            ->where('jenis', JenisHariLibur::Nasional)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('admin-hr.hari-libur.index', [
            'hariLibur' => $hariLibur,
            'tahun' => $tahun,
        ]);
    }

    public function create(): View
    {
        return view('admin-hr.hari-libur.create');
    }

    public function store(SimpanHariLiburRequest $request): RedirectResponse
    {
        $hariLibur = HariLibur::query()->create([
            ...$request->validated(),
            'jenis' => JenisHariLibur::Nasional,
        ]);

        return redirect()
            ->route('admin_hr.hari_libur.index', ['tahun' => $hariLibur->tanggal->year])
            ->with('sukses', "Hari libur {$hariLibur->nama} berhasil ditambahkan.");
    }

    public function edit(HariLibur $hariLibur): View
    {
        abort_unless($hariLibur->jenis === JenisHariLibur::Nasional, 404);

        return view('admin-hr.hari-libur.edit', compact('hariLibur'));
    }

    public function update(PerbaruiHariLiburRequest $request, HariLibur $hariLibur): RedirectResponse
    {
        abort_unless($hariLibur->jenis === JenisHariLibur::Nasional, 404);

        $hariLibur->update($request->validated());

        return redirect()
            ->route('admin_hr.hari_libur.index', ['tahun' => $hariLibur->tanggal->year])
            ->with('sukses', "Hari libur {$hariLibur->nama} berhasil diperbarui.");
    }

    public function destroy(HariLibur $hariLibur): RedirectResponse
    {
        abort_unless($hariLibur->jenis === JenisHariLibur::Nasional, 404);

        $tahun = $hariLibur->tanggal->year;
        $nama = $hariLibur->nama;
        $hariLibur->delete();

        return redirect()
            ->route('admin_hr.hari_libur.index', ['tahun' => $tahun])
            ->with('sukses', "Hari libur {$nama} berhasil dihapus.");
    }
}
