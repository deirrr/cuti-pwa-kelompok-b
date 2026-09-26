<?php

use App\Http\Controllers\AdminHr\DashboardController as AdminHrDashboardController;
use App\Http\Controllers\AdminHr\UnitBisnisController;
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

    Route::middleware('peran:admin_hr')->prefix('admin-hr')->name('admin_hr.')->group(function () {
        Route::get('/dashboard', AdminHrDashboardController::class)->name('dashboard');
        Route::get('/unit-bisnis', [UnitBisnisController::class, 'index'])->name('unit_bisnis.index');
        Route::get('/unit-bisnis/tambah', [UnitBisnisController::class, 'create'])->name('unit_bisnis.create');
        Route::post('/unit-bisnis', [UnitBisnisController::class, 'store'])->name('unit_bisnis.store');
        Route::get('/unit-bisnis/{unitBisnis}/ubah', [UnitBisnisController::class, 'edit'])->name('unit_bisnis.edit');
        Route::put('/unit-bisnis/{unitBisnis}', [UnitBisnisController::class, 'update'])->name('unit_bisnis.update');
    });

    Route::get('/dashboard/admin-hr', AdminHrDashboardController::class)
        ->middleware('peran:admin_hr')
        ->name('dashboard.admin_hr');
});
