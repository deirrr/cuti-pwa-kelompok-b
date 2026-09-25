@extends('layouts.app')

@section('judul', 'Masuk')

@section('konten')
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="hidden bg-emerald-950 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-white/10 text-sm font-semibold ring-1 ring-white/20">MA</span>
                <div>
                    <p class="font-semibold">PT Medika Antapani</p>
                    <p class="text-sm text-emerald-100">Sistem Informasi Cuti Karyawan</p>
                </div>
            </div>

            <div class="max-w-lg">
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-emerald-300">Portal internal</p>
                <h1 class="mt-4 text-4xl font-semibold leading-tight">Pengajuan dan persetujuan cuti dalam satu alur.</h1>
                <p class="mt-5 text-base leading-7 text-emerald-100">Masuk menggunakan NIK yang terdaftar untuk mengakses halaman sesuai peran Anda.</p>
            </div>

            <p class="text-sm text-emerald-200">Akses transaksi memerlukan koneksi internet.</p>
        </section>

        <section class="flex items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-700 text-sm font-semibold text-white">MA</span>
                    <div>
                        <p class="font-semibold">PT Medika Antapani</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Sistem Informasi Cuti</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Selamat datang</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight">Masuk ke akun Anda</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-400">Gunakan NIK dan kata sandi yang diberikan oleh Admin HR.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 flex flex-col gap-6">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="nomor_induk" class="text-sm font-medium">NIK</label>
                        <input
                            id="nomor_induk"
                            name="nomor_induk"
                            type="text"
                            value="{{ old('nomor_induk') }}"
                            maxlength="30"
                            autocomplete="username"
                            autofocus
                            required
                            aria-describedby="nomor_induk_error"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm uppercase shadow-sm outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-900"
                            placeholder="Contoh: PGW-000004"
                        >
                        @error('nomor_induk')
                            <p id="nomor_induk_error" class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="kata_sandi" class="text-sm font-medium">Kata sandi</label>
                        <input
                            id="kata_sandi"
                            name="kata_sandi"
                            type="password"
                            autocomplete="current-password"
                            required
                            aria-describedby="kata_sandi_error"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-900"
                            placeholder="Masukkan kata sandi"
                        >
                        @error('kata_sandi')
                            <p id="kata_sandi_error" class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 text-sm text-slate-700 dark:text-slate-300">
                        <input name="ingat_saya" type="checkbox" value="1" class="size-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                        Ingat saya di perangkat ini
                    </label>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">
                        Masuk
                    </button>
                </form>
            </div>
        </section>
    </main>
@endsection
