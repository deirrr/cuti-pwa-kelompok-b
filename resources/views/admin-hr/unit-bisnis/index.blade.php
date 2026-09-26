@extends('layouts.admin-hr')

@section('judul', 'Unit Bisnis')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Struktur Organisasi</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Unit Bisnis</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Kelola unit utama perusahaan. Data yang sudah digunakan sebaiknya dinonaktifkan, bukan dihapus.</p>
        </div>
        <a href="{{ route('admin_hr.unit_bisnis.create') }}" class="inline-flex w-fit items-center justify-center rounded-xl bg-emerald-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">Tambah Unit</a>
    </header>

    @if (session('sukses'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('sukses') }}</div>
    @endif

    <form method="GET" action="{{ route('admin_hr.unit_bisnis.index') }}" class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row">
        <label for="cari" class="sr-only">Cari unit bisnis</label>
        <input id="cari" name="cari" type="search" value="{{ $pencarian }}" placeholder="Cari kode atau nama unit" class="min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950">
        <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-500/20 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Cari</button>
        @if ($pencarian !== '')
            <a href="{{ route('admin_hr.unit_bisnis.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Reset</a>
        @endif
    </form>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm dark:divide-slate-800">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-semibold">Unit</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Kategori</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Departemen</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Jabatan</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Status</th>
                        <th scope="col" class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($unitBisnis as $unit)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-semibold">{{ $unit->nama }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $unit->kode }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $unit->kategori->label() }}</td>
                            <td class="px-5 py-4">{{ $unit->departemen_count }}</td>
                            <td class="px-5 py-4">{{ $unit->jabatan_count }}</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' => $unit->aktif,
                                    'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' => ! $unit->aktif,
                                ])>{{ $unit->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin_hr.unit_bisnis.edit', $unit) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300">Ubah</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">Tidak ada unit bisnis yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{ $unitBisnis->links() }}
@endsection
