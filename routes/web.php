<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SesiController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): RedirectResponse => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [SesiController::class, 'create'])->name('login');
    Route::post('/login', [SesiController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'akun.aktif'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [SesiController::class, 'destroy'])->name('logout');

    Route::view('/dashboard/karyawan', 'dashboard', ['jenisDashboard' => 'Karyawan'])
        ->middleware('peran:karyawan,atasan,admin_hr')
        ->name('dashboard.karyawan');

    Route::view('/dashboard/atasan', 'dashboard', ['jenisDashboard' => 'Atasan'])
        ->middleware('peran:atasan')
        ->name('dashboard.atasan');

    Route::view('/dashboard/admin-hr', 'dashboard', ['jenisDashboard' => 'Admin HR'])
        ->middleware('peran:admin_hr')
        ->name('dashboard.admin_hr');
});
