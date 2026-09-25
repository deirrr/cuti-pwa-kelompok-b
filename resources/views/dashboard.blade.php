@extends('layouts.app')

@section('judul', 'Dashboard '.$jenisDashboard)

@section('konten')
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-6 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-700 text-xs font-semibold text-white">MA</span>
                    <div class="min-w-0">
                        <p class="truncate font-semibold">Sistem Informasi Cuti</p>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">PT Medika Antapani</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-500/10 dark:border-slate-700 dark:hover:bg-slate-800">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <main class="mx-auto flex max-w-6xl flex-col gap-8 px-6 py-10">
            <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Dashboard {{ $jenisDashboard }}</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight">Halo, {{ auth()->user()->pegawai->nama }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Autentikasi dan pembatasan akses sudah aktif. Modul pengajuan serta persetujuan cuti masih dalam tahap pengembangan.</p>
                </div>
                <span class="w-fit rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Akun aktif</span>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">NIK</p>
                    <p class="mt-2 text-lg font-semibold">{{ auth()->user()->pegawai->nomor_induk }}</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Jabatan</p>
                    <p class="mt-2 text-lg font-semibold">{{ auth()->user()->pegawai->jabatan }}</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:col-span-2 lg:col-span-1 dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Peran sistem</p>
                    <p class="mt-2 text-lg font-semibold">{{ auth()->user()->peran->label() }}</p>
                </article>
            </section>

            <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 dark:border-slate-700 dark:bg-slate-900">
                <h2 class="text-lg font-semibold">Area akses tersedia</h2>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('dashboard.karyawan') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium transition hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700">Pengajuan pribadi</a>
                    @if (auth()->user()->peran === \App\Enums\PeranPengguna::Atasan)
                        <a href="{{ route('dashboard.atasan') }}" class="rounded-lg bg-emerald-100 px-4 py-2 text-sm font-medium text-emerald-800 transition hover:bg-emerald-200 dark:bg-emerald-950 dark:text-emerald-300">Area Atasan</a>
                    @endif
                    @if (auth()->user()->peran === \App\Enums\PeranPengguna::AdminHr)
                        <a href="{{ route('dashboard.admin_hr') }}" class="rounded-lg bg-emerald-100 px-4 py-2 text-sm font-medium text-emerald-800 transition hover:bg-emerald-200 dark:bg-emerald-950 dark:text-emerald-300">Area Admin HR</a>
                    @endif
                </div>
            </section>
        </main>
    </div>
@endsection
