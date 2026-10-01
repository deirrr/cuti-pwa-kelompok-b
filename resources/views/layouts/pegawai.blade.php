@extends('layouts.app')

@section('konten')
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-700 text-xs font-bold text-white">MA</span>
                    <span class="min-w-0">
                        <span class="block truncate font-bold">Medika Cuti</span>
                        <span class="block truncate text-xs text-slate-500">{{ auth()->user()->peran->label() }}</span>
                    </span>
                </a>

                <div class="flex items-center gap-2">
                    <nav aria-label="Navigasi pengguna" class="hidden items-center gap-1 md:flex">
                        <a href="{{ route('cuti.index') }}" @class([
                            'rounded-lg px-3 py-2 text-sm font-semibold transition',
                            'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => request()->routeIs('cuti.*'),
                            'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' => ! request()->routeIs('cuti.*'),
                        ])>Cuti Saya</a>
                        @if (auth()->user()->peran === \App\Enums\PeranPengguna::Atasan)
                            <a href="{{ route('persetujuan_atasan.index') }}" @class([
                                'rounded-lg px-3 py-2 text-sm font-semibold transition',
                                'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => request()->routeIs('persetujuan_atasan.*'),
                                'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' => ! request()->routeIs('persetujuan_atasan.*'),
                            ])>Persetujuan Staff</a>
                        @endif
                    </nav>
                    <button data-theme-toggle type="button" aria-label="Ubah tema" class="flex size-10 items-center justify-center rounded-full text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg class="size-5 dark:hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
                        <svg class="hidden size-5 dark:block" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Keluar</button>
                    </form>
                </div>
            </div>

            <nav aria-label="Navigasi pengguna seluler" class="mx-auto flex max-w-6xl gap-2 overflow-x-auto border-t border-slate-100 px-5 py-2 dark:border-slate-800 md:hidden">
                <a href="{{ route('cuti.index') }}" class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold text-emerald-700 dark:text-emerald-300">Cuti Saya</a>
                @if (auth()->user()->peran === \App\Enums\PeranPengguna::Atasan)
                    <a href="{{ route('persetujuan_atasan.index') }}" class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold text-emerald-700 dark:text-emerald-300">Persetujuan Staff</a>
                @endif
            </nav>
        </header>

        <main class="mx-auto flex max-w-6xl flex-col gap-8 px-5 py-8 sm:px-8 lg:py-10">
            @yield('konten-pegawai')
        </main>
    </div>
@endsection
