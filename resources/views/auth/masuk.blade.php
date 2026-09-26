@extends('layouts.app')

@section('judul', 'Masuk')

@section('konten')
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 px-5 py-10 dark:bg-slate-950 sm:px-8">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -left-32 -top-32 size-96 rounded-full bg-emerald-200/50 blur-3xl dark:bg-emerald-900/20"></div>
            <div class="absolute -bottom-40 -right-32 size-[30rem] rounded-full bg-teal-200/50 blur-3xl dark:bg-teal-900/20"></div>
            <div class="absolute inset-0 opacity-[0.035] dark:opacity-[0.06]" style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 24px 24px;"></div>
        </div>

        <div class="relative w-full max-w-md">
            <div class="mb-7 flex justify-center">
                <a href="{{ route('login') }}" class="flex items-center gap-3">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-600/25">MA</span>
                    <span>
                        <span class="block font-bold tracking-tight text-slate-900 dark:text-white">Medika Cuti</span>
                        <span class="block text-xs text-slate-500 dark:text-slate-400">PT Medika Antapani</span>
                    </span>
                </a>
            </div>

            <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl shadow-slate-900/5 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20 sm:p-9">
                <div class="text-center">
                    <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Selamat datang kembali</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Masuk ke akun Anda</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Gunakan NIK dan kata sandi yang diberikan Admin HR.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 flex flex-col gap-5">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="nomor_induk" class="text-sm font-semibold text-slate-700 dark:text-slate-200">NIK</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
                            <input id="nomor_induk" name="nomor_induk" type="text" value="{{ old('nomor_induk') }}" maxlength="30" autocomplete="username" autofocus required aria-describedby="nomor_induk_error" @class([
                                'w-full rounded-xl border bg-white py-3 pl-12 pr-4 text-sm uppercase outline-none transition placeholder:text-slate-400 focus:ring-4 dark:bg-slate-950',
                                'border-red-400 focus:border-red-500 focus:ring-red-500/10' => $errors->has('nomor_induk'),
                                'border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/10 dark:border-slate-700' => ! $errors->has('nomor_induk'),
                            ]) placeholder="Contoh: PGW-000004">
                        </div>
                        @error('nomor_induk')
                            <p id="nomor_induk_error" class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="kata_sandi" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Kata sandi</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input id="kata_sandi" name="kata_sandi" type="password" autocomplete="current-password" required aria-describedby="kata_sandi_error" @class([
                                'w-full rounded-xl border bg-white py-3 pl-12 pr-12 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 dark:bg-slate-950',
                                'border-red-400 focus:border-red-500 focus:ring-red-500/10' => $errors->has('kata_sandi'),
                                'border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/10 dark:border-slate-700' => ! $errors->has('kata_sandi'),
                            ]) placeholder="Masukkan kata sandi">
                            <button data-password-toggle type="button" aria-label="Tampilkan kata sandi" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                                <svg data-password-show-icon class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg data-password-hide-icon class="hidden size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 4.2A9.8 9.8 0 0 1 12 4c6.5 0 10 8 10 8a16 16 0 0 1-2 3M6.6 6.6C3.7 8.5 2 12 2 12s3.5 8 10 8a9.7 9.7 0 0 0 4.1-.9"/></svg>
                            </button>
                        </div>
                        @error('kata_sandi')
                            <p id="kata_sandi_error" class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex w-fit cursor-pointer items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                        <input name="ingat_saya" type="checkbox" value="1" class="size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950">
                        Ingat saya di perangkat ini
                    </label>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">
                        Masuk
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </form>
            </section>

            <div class="mt-6 flex items-center justify-between gap-4 px-2 text-xs text-slate-500 dark:text-slate-400">
                <p>Transaksi memerlukan koneksi internet.</p>
                <button data-theme-toggle type="button" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-2 py-1.5 font-medium hover:bg-white hover:text-emerald-700 dark:hover:bg-slate-900 dark:hover:text-emerald-400">
                    <svg class="size-4 dark:hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
                    <svg class="hidden size-4 dark:block" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                    Tema
                </button>
            </div>
            <p class="mt-4 text-center text-[10px] text-slate-400">UI diadaptasi dari TailwindAdmin &middot; MIT</p>
        </div>
    </main>
@endsection
