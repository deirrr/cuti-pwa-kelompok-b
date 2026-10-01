<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Models\BagianOrganisasi;
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
            'jumlahStaffAktif' => Pegawai::query()
                ->where('aktif', true)
                ->whereHas('pengguna', fn ($query) => $query->where('peran', PeranPengguna::Karyawan->value))
                ->count(),
            'jumlahAtasanAktif' => Pegawai::query()
                ->where('aktif', true)
                ->whereHas('pengguna', fn ($query) => $query->where('peran', PeranPengguna::Atasan->value))
                ->count(),
        ]);
    }
}
