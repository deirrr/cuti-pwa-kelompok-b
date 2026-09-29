<?php

namespace App\Http\Controllers\AdminHr;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\PerbaruiBagianOrganisasiRequest;
use App\Http\Requests\AdminHr\SimpanBagianOrganisasiRequest;
use App\Models\BagianOrganisasi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BagianOrganisasiController extends Controller
{
    public function index(Request $request): View
    {
        $pencarian = $request->string('cari')->trim()->toString();
        $bagianOrganisasi = BagianOrganisasi::query()
            ->withCount('jabatan')
            ->when($pencarian !== '', function ($query) use ($pencarian): void {
                $query->where(function ($query) use ($pencarian): void {
                    $query
                        ->where('kode', 'like', "%{$pencarian}%")
                        ->orWhere('nama', 'like', "%{$pencarian}%");
                });
            })
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin-hr.bagian-organisasi.index', [
            'bagianOrganisasi' => $bagianOrganisasi,
            'pencarian' => $pencarian,
        ]);
    }

    public function create(): View
    {
        return view('admin-hr.bagian-organisasi.create');
    }

    public function store(SimpanBagianOrganisasiRequest $request): RedirectResponse
    {
        $bagianOrganisasi = BagianOrganisasi::query()->create($request->validated());

        return redirect()
            ->route('admin_hr.bagian_organisasi.index')
            ->with('sukses', "Bagian {$bagianOrganisasi->nama} berhasil ditambahkan.");
    }

    public function edit(BagianOrganisasi $bagianOrganisasi): View
    {
        return view('admin-hr.bagian-organisasi.edit', compact('bagianOrganisasi'));
    }

    public function update(
        PerbaruiBagianOrganisasiRequest $request,
        BagianOrganisasi $bagianOrganisasi,
    ): RedirectResponse {
        $bagianOrganisasi->update($request->validated());

        return redirect()
            ->route('admin_hr.bagian_organisasi.index')
            ->with('sukses', "Bagian {$bagianOrganisasi->nama} berhasil diperbarui.");
    }
}
