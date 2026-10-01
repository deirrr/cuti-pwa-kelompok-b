@extends('layouts.pegawai')

@section('judul', 'Dashboard '.$jenisDashboard)

@section('konten-pegawai')
    <header>
        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Dashboard {{ $jenisDashboard }}</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Halo, {{ auth()->user()->pegawai->nama }}</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-400">Ajukan cuti dan pantau proses persetujuannya dalam satu tempat.</p>
    </header>

    <section class="grid gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm text-slate-500">NIK</p>
            <p class="mt-2 font-bold">{{ auth()->user()->pegawai->nomor_induk }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm text-slate-500">Bagian organisasi</p>
            <p class="mt-2 font-bold">{{ auth()->user()->pegawai->bagianOrganisasi->nama }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm text-slate-500">Jatah cuti</p>
            <p class="mt-2 font-bold">{{ auth()->user()->pegawai->jatah_cuti }} hari</p>
        </article>
    </section>

    <section class="grid gap-5 md:grid-cols-2">
        <a href="{{ route('cuti.index') }}" class="group rounded-2xl bg-emerald-700 p-6 text-white shadow-lg shadow-emerald-900/10 transition hover:bg-emerald-800">
            <span class="flex size-11 items-center justify-center rounded-xl bg-white/15">
                <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/></svg>
            </span>
            <h2 class="mt-5 text-xl font-bold">Cuti Saya</h2>
            <p class="mt-2 text-sm leading-6 text-emerald-100">Buat pengajuan baru dan lihat seluruh riwayat pengajuan Anda.</p>
        </a>

        @if (auth()->user()->peran === \App\Enums\PeranPengguna::Atasan)
            <a href="{{ route('persetujuan_atasan.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-800">
                <span class="flex size-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                    <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </span>
                <h2 class="mt-5 text-xl font-bold">Persetujuan Staff</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Tinjau pengajuan dari Staff yang berada di bawah tanggung jawab Anda.</p>
            </a>
        @endif
    </section>
@endsection
