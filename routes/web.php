<?php

use App\Http\Controllers\AdminHr\BagianOrganisasiController;
use App\Http\Controllers\AdminHr\DashboardController as AdminHrDashboardController;
use App\Http\Controllers\AdminHr\PegawaiController;
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
        Route::get('/bagian-organisasi', [BagianOrganisasiController::class, 'index'])->name('bagian_organisasi.index');
        Route::get('/bagian-organisasi/tambah', [BagianOrganisasiController::class, 'create'])->name('bagian_organisasi.create');
        Route::post('/bagian-organisasi', [BagianOrganisasiController::class, 'store'])->name('bagian_organisasi.store');
        Route::get('/bagian-organisasi/{bagianOrganisasi}/ubah', [BagianOrganisasiController::class, 'edit'])->name('bagian_organisasi.edit');
        Route::put('/bagian-organisasi/{bagianOrganisasi}', [BagianOrganisasiController::class, 'update'])->name('bagian_organisasi.update');
        Route::get('/karyawan', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/karyawan/tambah', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/karyawan', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/karyawan/{pegawai}/ubah', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/karyawan/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
    });

    Route::get('/dashboard/admin-hr', AdminHrDashboardController::class)
        ->middleware('peran:admin_hr')
        ->name('dashboard.admin_hr');
});
