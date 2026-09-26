<?php

namespace App\Http\Controllers\AdminHr;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\UnitBisnis;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        return view('admin-hr.dashboard', [
            'jumlahUnitAktif' => UnitBisnis::query()->where('aktif', true)->count(),
            'jumlahDepartemenAktif' => Departemen::query()->where('aktif', true)->count(),
            'jumlahJabatanAktif' => Jabatan::query()->where('aktif', true)->count(),
            'jumlahPegawaiAktif' => Pegawai::query()->where('aktif', true)->count(),
        ]);
    }
}
