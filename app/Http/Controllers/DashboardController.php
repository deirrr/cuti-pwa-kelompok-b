<?php

namespace App\Http\Controllers;

use App\Enums\PeranPengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        return match ($request->user()->peran) {
            PeranPengguna::Karyawan => redirect()->route('dashboard.karyawan'),
            PeranPengguna::Atasan => redirect()->route('dashboard.atasan'),
            PeranPengguna::AdminHr => redirect()->route('dashboard.admin_hr'),
        };
    }
}
