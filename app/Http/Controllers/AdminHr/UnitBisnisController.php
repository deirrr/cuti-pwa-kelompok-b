<?php

namespace App\Http\Controllers\AdminHr;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\PerbaruiUnitBisnisRequest;
use App\Http\Requests\AdminHr\SimpanUnitBisnisRequest;
use App\Models\UnitBisnis;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UnitBisnisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pencarian = $request->string('cari')->trim()->toString();

        $unitBisnis = UnitBisnis::query()
            ->withCount(['departemen', 'jabatan'])
            ->when($pencarian !== '', function ($query) use ($pencarian): void {
                $query->where(function ($query) use ($pencarian): void {
                    $query
                        ->where('kode', 'like', "%{$pencarian}%")
                        ->orWhere('nama', 'like', "%{$pencarian}%");
                });
            })
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin-hr.unit-bisnis.index', [
            'unitBisnis' => $unitBisnis,
            'pencarian' => $pencarian,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin-hr.unit-bisnis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SimpanUnitBisnisRequest $request): RedirectResponse
    {
        $unitBisnis = UnitBisnis::query()->create($request->validated());

        return redirect()
            ->route('admin_hr.unit_bisnis.index')
            ->with('sukses', "Unit {$unitBisnis->nama} berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function edit(UnitBisnis $unitBisnis): View
    {
        return view('admin-hr.unit-bisnis.edit', ['unitBisnis' => $unitBisnis]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PerbaruiUnitBisnisRequest $request, UnitBisnis $unitBisnis): RedirectResponse
    {
        $unitBisnis->update($request->validated());

        return redirect()
            ->route('admin_hr.unit_bisnis.index')
            ->with('sukses', "Unit {$unitBisnis->nama} berhasil diperbarui.");
    }
}
