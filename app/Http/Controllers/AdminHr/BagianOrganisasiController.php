<?php

namespace App\Http\Controllers\AdminHr;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\PerbaruiBagianOrganisasiRequest;
use App\Http\Requests\AdminHr\SimpanBagianOrganisasiRequest;
use App\Models\BagianOrganisasi;
use App\Models\UnitBisnis;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BagianOrganisasiController extends Controller
{
    public function index(Request $request): View
    {
        $pencarian = $request->string('cari')->trim()->toString();
        $unitBisnisId = $request->integer('unit_bisnis_id');

        $bagianOrganisasi = BagianOrganisasi::query()
            ->with(['unitBisnis', 'induk'])
            ->withCount(['anak', 'jabatan'])
            ->when($pencarian !== '', function ($query) use ($pencarian): void {
                $query->where(function ($query) use ($pencarian): void {
                    $query
                        ->where('kode', 'like', "%{$pencarian}%")
                        ->orWhere('nama', 'like', "%{$pencarian}%");
                });
            })
            ->when($unitBisnisId > 0, fn ($query) => $query->where('unit_bisnis_id', $unitBisnisId))
            ->orderByDesc('aktif')
            ->orderBy('unit_bisnis_id')
            ->orderBy('nama')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin-hr.bagian-organisasi.index', [
            'bagianOrganisasi' => $bagianOrganisasi,
            'daftarUnitBisnis' => $this->daftarUnitBisnis(),
            'pencarian' => $pencarian,
            'unitBisnisId' => $unitBisnisId,
        ]);
    }

    public function create(): View
    {
        return view('admin-hr.bagian-organisasi.create', [
            'daftarUnitBisnis' => $this->daftarUnitBisnis(hanyaAktif: true),
            'daftarInduk' => $this->daftarInduk(),
        ]);
    }

    public function store(SimpanBagianOrganisasiRequest $request): RedirectResponse
    {
        $bagianOrganisasi = BagianOrganisasi::query()->create($request->validated());

        return redirect()
            ->route('admin_hr.bagian_organisasi.index')
            ->with('sukses', "{$bagianOrganisasi->jenis->label()} {$bagianOrganisasi->nama} berhasil ditambahkan.");
    }

    public function edit(BagianOrganisasi $bagianOrganisasi): View
    {
        return view('admin-hr.bagian-organisasi.edit', [
            'bagianOrganisasi' => $bagianOrganisasi,
            'daftarUnitBisnis' => $this->daftarUnitBisnis(),
            'daftarInduk' => $this->daftarInduk($bagianOrganisasi),
        ]);
    }

    public function update(
        PerbaruiBagianOrganisasiRequest $request,
        BagianOrganisasi $bagianOrganisasi,
    ): RedirectResponse {
        $bagianOrganisasi->update($request->validated());

        return redirect()
            ->route('admin_hr.bagian_organisasi.index')
            ->with('sukses', "{$bagianOrganisasi->jenis->label()} {$bagianOrganisasi->nama} berhasil diperbarui.");
    }

    /** @return Collection<int, UnitBisnis> */
    private function daftarUnitBisnis(bool $hanyaAktif = false): Collection
    {
        return UnitBisnis::query()
            ->when($hanyaAktif, fn ($query) => $query->where('aktif', true))
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->get();
    }

    /** @return Collection<int, BagianOrganisasi> */
    private function daftarInduk(?BagianOrganisasi $dikecualikan = null): Collection
    {
        return BagianOrganisasi::query()
            ->with('unitBisnis')
            ->where('aktif', true)
            ->when($dikecualikan !== null, fn ($query) => $query->whereKeyNot($dikecualikan->getKey()))
            ->orderBy('unit_bisnis_id')
            ->orderBy('nama')
            ->get();
    }
}
