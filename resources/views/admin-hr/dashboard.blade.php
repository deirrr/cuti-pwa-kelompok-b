@extends('layouts.admin-hr')

@section('judul', 'Dashboard Admin HR')

@section('konten-hr')
    <header>
        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Dashboard Admin HR</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Kelola fondasi organisasi</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-400">Mulai dari unit bisnis, kemudian lanjutkan ke departemen, jabatan, dan penugasan pegawai. Alur persetujuan akan menggunakan data tersebut.</p>
    </header>

    <section aria-label="Ringkasan data organisasi" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Unit aktif', 'nilai' => $jumlahUnitAktif],
            ['label' => 'Departemen aktif', 'nilai' => $jumlahDepartemenAktif],
            ['label' => 'Jabatan aktif', 'nilai' => $jumlahJabatanAktif],
            ['label' => 'Pegawai aktif', 'nilai' => $jumlahPegawaiAktif],
        ] as $ringkasan)
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $ringkasan['label'] }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight">{{ $ringkasan['nilai'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-semibold">Unit Bisnis</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Enam unit awal sudah tersedia dan dapat diperbarui tanpa menghapus riwayat.</p>
            </div>
            <a href="{{ route('admin_hr.unit_bisnis.index') }}" class="inline-flex w-fit items-center justify-center rounded-xl bg-emerald-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">Kelola Unit Bisnis</a>
        </div>
    </section>
@endsection
