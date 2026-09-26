@extends('layouts.admin-hr')

@section('judul', 'Dashboard Admin HR')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <span>Admin HR</span>
                <span aria-hidden="true">/</span>
                <span class="font-medium text-slate-800 dark:text-slate-200">Ringkasan</span>
            </div>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola fondasi organisasi</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-400">Data organisasi menjadi dasar penentuan atasan dan alur persetujuan cuti setiap karyawan.</p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:ring-emerald-900">
            <span class="size-2 rounded-full bg-emerald-500"></span>
            Sistem aktif
        </span>
    </header>

    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-700 to-teal-800 p-6 text-white shadow-xl shadow-emerald-950/10 sm:p-8">
        <div class="absolute -right-20 -top-24 size-72 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-24 right-32 size-56 rounded-full bg-teal-300/10"></div>
        <div class="relative max-w-2xl">
            <p class="text-sm font-semibold text-emerald-100">Selamat datang, {{ auth()->user()->pegawai->nama }}</p>
            <h2 class="mt-3 text-2xl font-bold tracking-tight sm:text-3xl">Siapkan struktur organisasi sebelum alur cuti digunakan.</h2>
            <p class="mt-4 text-sm leading-6 text-emerald-50/90">Mulai dari unit bisnis, lalu lanjutkan dengan departemen, jabatan, dan penugasan pegawai sesuai tahapan pengembangan.</p>
            <a href="{{ route('admin_hr.unit_bisnis.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-semibold text-emerald-800 shadow-sm transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/30">
                Kelola Unit Bisnis
                <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>
    </section>

    @php
        $ringkasan = [
            ['label' => 'Unit aktif', 'nilai' => $jumlahUnitAktif, 'warna' => 'emerald', 'ikon' => 'M3 21h18M5 21V7l7-4 7 4v14M9 10h1m4 0h1m-6 4h1m4 0h1m-6 4h6'],
            ['label' => 'Departemen aktif', 'nilai' => $jumlahDepartemenAktif, 'warna' => 'blue', 'ikon' => 'M4 21V10l8-5 8 5v11M9 21v-6h6v6'],
            ['label' => 'Jabatan aktif', 'nilai' => $jumlahJabatanAktif, 'warna' => 'violet', 'ikon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
            ['label' => 'Pegawai aktif', 'nilai' => $jumlahPegawaiAktif, 'warna' => 'amber', 'ikon' => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
        ];
    @endphp

    <section aria-label="Ringkasan data organisasi" class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($ringkasan as $item)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $item['label'] }}</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $item['nilai'] }}</p>
                    </div>
                    <span @class([
                        'flex size-12 items-center justify-center rounded-xl',
                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $item['warna'] === 'emerald',
                        'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' => $item['warna'] === 'blue',
                        'bg-violet-100 text-violet-700 dark:bg-violet-950 dark:text-violet-300' => $item['warna'] === 'violet',
                        'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' => $item['warna'] === 'amber',
                    ])>
                        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="{{ $item['ikon'] }}"/></svg>
                    </span>
                </div>
                <p class="mt-4 text-xs text-slate-400 dark:text-slate-500">Data aktif saat ini</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-7">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div class="flex items-start gap-4">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                    <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1m4 0h1m-6 4h1m4 0h1m-6 4h6"/></svg>
                </span>
                <div>
                    <h2 class="font-bold text-slate-900 dark:text-white">Unit Bisnis</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">Enam unit awal dapat diperbarui atau dinonaktifkan tanpa menghapus riwayat.</p>
                </div>
            </div>
            <a href="{{ route('admin_hr.unit_bisnis.index') }}" class="inline-flex w-fit items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-emerald-950">Lihat Data</a>
        </div>
    </section>
@endsection
