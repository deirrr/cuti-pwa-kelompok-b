@extends('layouts.admin-hr')

@section('judul', 'Bagian Organisasi')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin_hr.dashboard') }}" class="hover:text-emerald-700 dark:hover:text-emerald-400">Admin HR</a>
                <span aria-hidden="true">/</span>
                <span class="font-medium text-slate-800 dark:text-slate-200">Bagian Organisasi</span>
            </div>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Bagian Organisasi</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Gunakan Direktorat dan Departemen untuk Head Office, sedangkan unit operasional menggunakan Bagian.</p>
        </div>
        <a href="{{ route('admin_hr.bagian_organisasi.create') }}" class="inline-flex w-fit items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">
            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Bagian Organisasi
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
                <h2 class="font-bold text-slate-900 dark:text-white">Daftar Struktur</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Direktorat, departemen, dan bagian pada setiap unit bisnis</p>
            </div>
            <form method="GET" action="{{ route('admin_hr.bagian_organisasi.index') }}" class="grid gap-2 sm:grid-cols-[minmax(0,14rem)_minmax(0,16rem)_auto]">
                <label for="unit_bisnis_id" class="sr-only">Filter unit bisnis</label>
                <select id="unit_bisnis_id" name="unit_bisnis_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Semua unit bisnis</option>
                    @foreach ($daftarUnitBisnis as $unit)
                        <option value="{{ $unit->getKey() }}" @selected($unitBisnisId === $unit->getKey())>{{ $unit->nama }}</option>
                    @endforeach
                </select>
                <label for="cari" class="sr-only">Cari bagian organisasi</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input id="cari" name="cari" type="search" value="{{ $pencarian }}" placeholder="Cari kode atau nama" class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Terapkan</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-950/50 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-semibold">Nama</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Unit Bisnis</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Jenis</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Induk</th>
                        <th scope="col" class="px-5 py-4 text-center font-semibold">Jabatan</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Status</th>
                        <th scope="col" class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($bagianOrganisasi as $bagian)
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $bagian->nama }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $bagian->kode }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $bagian->unitBisnis->nama }}</td>
                            <td class="px-5 py-4"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300">{{ $bagian->jenis->label() }}</span></td>
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $bagian->induk?->nama ?? 'Langsung di bawah unit' }}</td>
                            <td class="px-5 py-4 text-center font-medium">{{ $bagian->jabatan_count }}</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $bagian->aktif,
                                    'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' => ! $bagian->aktif,
                                ])><span @class(['size-1.5 rounded-full', 'bg-emerald-500' => $bagian->aktif, 'bg-slate-400' => ! $bagian->aktif])></span>{{ $bagian->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-5 py-4 text-right"><a href="{{ route('admin_hr.bagian_organisasi.edit', $bagian) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 font-semibold text-emerald-700 transition hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950">Ubah</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-14 text-center text-slate-500 dark:text-slate-400">Belum ada bagian organisasi yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-800">{{ $bagianOrganisasi->links() }}</div>
    </section>
@endsection
