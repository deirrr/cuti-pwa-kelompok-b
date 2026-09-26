@extends('layouts.app')

@section('konten')
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div data-sidebar-overlay data-sidebar-close class="fixed inset-0 z-40 hidden bg-slate-950/50 backdrop-blur-sm xl:hidden"></div>

        <aside data-sidebar class="fixed inset-y-0 left-0 z-50 flex w-[270px] -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-300 dark:border-slate-800 dark:bg-slate-900 xl:translate-x-0">
            <div class="flex h-20 items-center justify-between border-b border-slate-100 px-6 dark:border-slate-800">
                <a href="{{ route('admin_hr.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-600/20">MA</span>
                    <span class="min-w-0">
                        <span class="block truncate text-base font-bold tracking-tight text-slate-900 dark:text-white">Medika Cuti</span>
                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">Portal Admin HR</span>
                    </span>
                </a>
                <button data-sidebar-close type="button" aria-label="Tutup navigasi" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 xl:hidden">
                    <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <nav aria-label="Navigasi Admin HR" class="flex-1 overflow-y-auto px-4 py-6">
                <p class="px-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">Beranda</p>
                <div class="mt-2 flex flex-col gap-1">
                    <a href="{{ route('admin_hr.dashboard') }}" @class([
                        'group flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition',
                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' => request()->routeIs('admin_hr.dashboard') || request()->routeIs('dashboard.admin_hr'),
                        'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' => ! request()->routeIs('admin_hr.dashboard') && ! request()->routeIs('dashboard.admin_hr'),
                    ])>
                        <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
                        Ringkasan
                    </a>
                </div>

                <p class="mt-7 px-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">Organisasi</p>
                <div class="mt-2 flex flex-col gap-1">
                    <a href="{{ route('admin_hr.unit_bisnis.index') }}" @class([
                        'group flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition',
                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' => request()->routeIs('admin_hr.unit_bisnis.*'),
                        'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' => ! request()->routeIs('admin_hr.unit_bisnis.*'),
                    ])>
                        <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1m4 0h1m-6 4h1m4 0h1m-6 4h6"/></svg>
                        Unit Bisnis
                    </a>
                    @foreach ([
                        ['label' => 'Departemen', 'icon' => 'M4 21V10l8-5 8 5v11M9 21v-6h6v6'],
                        ['label' => 'Jabatan', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
                        ['label' => 'Pegawai', 'icon' => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                    ] as $menu)
                        <span class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-400 dark:text-slate-600">
                            <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="{{ $menu['icon'] }}"/></svg>
                            {{ $menu['label'] }}
                            <span class="ml-auto rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase dark:bg-slate-800">Segera</span>
                        </span>
                    @endforeach
                </div>

                <p class="mt-7 px-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">Cuti Karyawan</p>
                <div class="mt-2 flex flex-col gap-1">
                    @foreach (['Persetujuan Akhir', 'Jenis & Saldo Cuti', 'Hari Libur', 'Rekap'] as $menu)
                        <span class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-400 dark:text-slate-600">
                            <span class="size-2 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            {{ $menu }}
                        </span>
                    @endforeach
                </div>
            </nav>

            <div class="border-t border-slate-100 p-4 dark:border-slate-800">
                <div class="rounded-2xl bg-emerald-50 p-4 dark:bg-emerald-950/50">
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white">{{ str(auth()->user()->pegawai->nama)->substr(0, 2)->upper() }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()->pegawai->nama }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->pegawai->nomor_induk }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-white px-3 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus:ring-4 focus:ring-emerald-600/15 dark:border-emerald-900 dark:bg-slate-900 dark:text-emerald-300 dark:hover:bg-emerald-950">
                            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5m5 5H9"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
                <p class="mt-3 text-center text-[10px] leading-4 text-slate-400">UI diadaptasi dari TailwindAdmin · MIT</p>
            </div>
        </aside>

        <div class="min-w-0 xl:pl-[270px]">
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90">
                <div class="flex h-20 items-center justify-between gap-4 px-5 sm:px-8 lg:px-10">
                    <div class="flex min-w-0 items-center gap-3">
                        <button data-sidebar-open type="button" aria-label="Buka navigasi" class="rounded-full p-2.5 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/10 dark:text-slate-300 dark:hover:bg-slate-800 xl:hidden">
                            <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div class="hidden items-center gap-3 rounded-xl bg-slate-100 px-4 py-2.5 text-sm text-slate-500 dark:bg-slate-800 dark:text-slate-400 sm:flex">
                            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            Cari menu atau data
                            <kbd class="ml-6 rounded border border-slate-300 bg-white px-1.5 py-0.5 text-[10px] dark:border-slate-700 dark:bg-slate-900">Ctrl K</kbd>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button data-theme-toggle type="button" aria-label="Ubah tema" class="rounded-full p-2.5 text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/10 dark:text-slate-300 dark:hover:bg-slate-800">
                            <svg data-theme-light-icon class="size-5 dark:hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
                            <svg data-theme-dark-icon class="hidden size-5 dark:block" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                        </button>
                        <span class="hidden h-8 w-px bg-slate-200 dark:bg-slate-700 sm:block"></span>
                        <div class="flex items-center gap-3 pl-1">
                            <span class="flex size-10 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ str(auth()->user()->pegawai->nama)->substr(0, 2)->upper() }}</span>
                            <div class="hidden min-w-0 sm:block">
                                <p class="max-w-36 truncate text-sm font-semibold">{{ auth()->user()->pegawai->nama }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Admin HR</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto flex max-w-[1440px] flex-col gap-8 px-5 py-8 sm:px-8 lg:px-10 lg:py-10">
                @yield('konten-hr')
            </main>
        </div>
    </div>
@endsection
