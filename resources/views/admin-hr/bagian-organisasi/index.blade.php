@extends('layouts.admin-hr')

@section('judul', 'Bagian Organisasi')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Klinik Utama Medika Antapani</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight">Bagian Organisasi</h1>
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Kelola bagian tempat Staff dan Atasan bekerja.</p>
        </div>
        <a href="{{ route('admin_hr.bagian_organisasi.create') }}" class="rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white">Tambah Bagian</a>
    </header>

    @if (session('sukses'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('sukses') }}</div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col justify-between gap-4 border-b border-slate-200 p-5 dark:border-slate-800 sm:flex-row sm:items-center">
            <h2 class="font-bold">Daftar Bagian</h2>
            <form method="GET" action="{{ route('admin_hr.bagian_organisasi.index') }}" class="flex gap-2">
                <input name="cari" type="search" value="{{ $pencarian }}" placeholder="Cari kode atau nama" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950">
                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950/50">
                    <tr><th class="px-5 py-4">Nama</th><th class="px-5 py-4">Jabatan</th><th class="px-5 py-4">Status</th><th class="px-5 py-4 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($bagianOrganisasi as $bagian)
                        <tr>
                            <td class="px-5 py-4"><p class="font-semibold">{{ $bagian->nama }}</p><p class="text-xs text-slate-500">{{ $bagian->kode }}</p></td>
                            <td class="px-5 py-4">{{ $bagian->jabatan_count }}</td>
                            <td class="px-5 py-4">{{ $bagian->aktif ? 'Aktif' : 'Nonaktif' }}</td>
                            <td class="px-5 py-4 text-right"><a href="{{ route('admin_hr.bagian_organisasi.edit', $bagian) }}" class="font-semibold text-emerald-700 dark:text-emerald-400">Ubah</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-14 text-center text-slate-500">Belum ada bagian organisasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-800">{{ $bagianOrganisasi->links() }}</div>
    </section>
@endsection
