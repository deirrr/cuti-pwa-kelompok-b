<?php

namespace App\Http\Controllers\AdminHr;

use App\Http\Controllers\Controller;
use App\Models\BagianOrganisasi;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        return view('admin-hr.dashboard', [
            'jumlahBagianOrganisasiAktif' => BagianOrganisasi::query()->where('aktif', true)->count(),
            'jumlahJabatanAktif' => Jabatan::query()->where('aktif', true)->count(),
            'jumlahPegawaiAktif' => Pegawai::query()->where('aktif', true)->count(),
        ]);
    }
}
