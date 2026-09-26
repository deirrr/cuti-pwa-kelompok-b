@extends('layouts.app')

@section('konten')
    <div class="min-h-screen lg:grid lg:grid-cols-[17rem_1fr]">
        <aside class="border-b border-slate-200 bg-slate-950 text-white lg:min-h-screen lg:border-b-0 lg:border-r lg:border-slate-800">
            <div class="flex items-center justify-between gap-4 px-5 py-5 lg:px-6">
                <a href="{{ route('admin_hr.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-xs font-bold">MA</span>
                    <span class="min-w-0">
                        <span class="block truncate font-semibold">Admin HR</span>
                        <span class="block truncate text-xs text-slate-400">PT Medika Antapani</span>
                    </span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-700 px-3 py-2 text-xs font-medium hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-emerald-500/20">Keluar</button>
                </form>
            </div>

            <nav aria-label="Navigasi Admin HR" class="flex gap-2 overflow-x-auto px-5 pb-5 lg:flex-col lg:px-4">
                <a href="{{ route('admin_hr.dashboard') }}" @class([
                    'whitespace-nowrap rounded-xl px-4 py-3 text-sm font-medium transition',
                    'bg-emerald-600 text-white' => request()->routeIs('admin_hr.dashboard'),
                    'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin_hr.dashboard'),
                ])>Ringkasan</a>
                <a href="{{ route('admin_hr.unit_bisnis.index') }}" @class([
                    'whitespace-nowrap rounded-xl px-4 py-3 text-sm font-medium transition',
                    'bg-emerald-600 text-white' => request()->routeIs('admin_hr.unit_bisnis.*'),
                    'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin_hr.unit_bisnis.*'),
                ])>Unit Bisnis</a>
                <span class="whitespace-nowrap rounded-xl px-4 py-3 text-sm text-slate-500">Departemen — segera</span>
                <span class="whitespace-nowrap rounded-xl px-4 py-3 text-sm text-slate-500">Jabatan — segera</span>
                <span class="whitespace-nowrap rounded-xl px-4 py-3 text-sm text-slate-500">Pegawai — segera</span>
            </nav>

            <div class="hidden px-4 pb-6 lg:block">
                <div class="border-t border-slate-800 pt-5">
                    <p class="truncate px-4 text-sm font-medium">{{ auth()->user()->pegawai->nama }}</p>
                    <p class="truncate px-4 text-xs text-slate-400">{{ auth()->user()->pegawai->nomor_induk }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-slate-700 px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white focus:outline-none focus:ring-4 focus:ring-emerald-500/20">Keluar</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="min-w-0">
            <div class="mx-auto flex max-w-7xl flex-col gap-8 px-5 py-8 sm:px-8 lg:px-10 lg:py-10">
                @yield('konten-hr')
            </div>
        </main>
    </div>
@endsection
