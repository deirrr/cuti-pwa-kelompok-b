<?php

namespace App\Http\Controllers;

use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if ($pengguna->memilikiPeran(PeranPengguna::AdminHr)) {
            return redirect()->route('dashboard.admin_hr');
        }

        if ($pengguna->memilikiPeran(PeranPengguna::Atasan)) {
            return redirect()->route('dashboard.atasan');
        }

        return redirect()->route('dashboard.karyawan');
    }
}
