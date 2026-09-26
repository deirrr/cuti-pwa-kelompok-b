@extends('layouts.admin-hr')

@section('judul', 'Unit Bisnis')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin_hr.dashboard') }}" class="hover:text-emerald-700 dark:hover:text-emerald-400">Admin HR</a>
                <span aria-hidden="true">/</span>
                <span class="font-medium text-slate-800 dark:text-slate-200">Unit Bisnis</span>
            </div>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Unit Bisnis</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Kelola unit utama perusahaan. Data yang sudah digunakan sebaiknya dinonaktifkan, bukan dihapus.</p>
        </div>
        <a href="{{ route('admin_hr.unit_bisnis.create') }}" class="inline-flex w-fit items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">
            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Unit
        </a>
    </header>

    @if (session('sukses'))
        <div role="status" class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
            <svg class="mt-0.5 size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
            {{ session('sukses') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col justify-between gap-4 border-b border-slate-200 p-5 dark:border-slate-800 lg:flex-row lg:items-center">
            <div>
                <h2 class="font-bold text-slate-900 dark:text-white">Daftar Unit</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Unit organisasi PT Medika Antapani</p>
            </div>
            <form method="GET" action="{{ route('admin_hr.unit_bisnis.index') }}" class="flex flex-col gap-2 sm:flex-row">
                <label for="cari" class="sr-only">Cari unit bisnis</label>
                <div class="relative min-w-0 sm:w-72">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input id="cari" name="cari" type="search" value="{{ $pencarian }}" placeholder="Cari kode atau nama unit" class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-500/20 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Cari</button>
                @if ($pencarian !== '')
                    <a href="{{ route('admin_hr.unit_bisnis.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-950/50 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-semibold">Unit</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Kategori</th>
                        <th scope="col" class="px-5 py-4 text-center font-semibold">Departemen</th>
                        <th scope="col" class="px-5 py-4 text-center font-semibold">Jabatan</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Status</th>
                        <th scope="col" class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($unitBisnis as $unit)
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ str($unit->kode)->substr(0, 2)->upper() }}</span>
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $unit->nama }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $unit->kode }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600 dark:text-slate-300">{{ $unit->kategori->label() }}</td>
                            <td class="px-5 py-4 text-center font-medium">{{ $unit->departemen_count }}</td>
                            <td class="px-5 py-4 text-center font-medium">{{ $unit->jabatan_count }}</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-900' => $unit->aktif,
                                    'bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700' => ! $unit->aktif,
                                ])><span @class(['size-1.5 rounded-full', 'bg-emerald-500' => $unit->aktif, 'bg-slate-400' => ! $unit->aktif])></span>{{ $unit->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin_hr.unit_bisnis.edit', $unit) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 font-semibold text-emerald-700 transition hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950">
                                    <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                                    Ubah
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <p class="font-medium text-slate-700 dark:text-slate-200">Tidak ada unit bisnis yang sesuai.</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Coba gunakan kata kunci lain atau reset pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-800">
            {{ $unitBisnis->links() }}
        </div>
    </section>
@endsection
