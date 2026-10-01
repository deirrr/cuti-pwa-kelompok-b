@extends('layouts.admin-hr')

@section('judul', 'Hari Libur Nasional')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Pengaturan Cuti</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight">Hari Libur Nasional</h1>
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Tanggal aktif tidak dapat dipilih oleh Staff maupun Atasan saat mengajukan cuti.</p>
        </div>
        <a href="{{ route('admin_hr.hari_libur.create') }}" class="rounded-xl bg-emerald-600 px-4 py-3 text-center text-sm font-semibold text-white">Tambah Hari Libur</a>
    </header>

    @if (session('sukses'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('sukses') }}</div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col justify-between gap-4 border-b border-slate-200 p-5 dark:border-slate-800 sm:flex-row sm:items-center">
            <div>
                <h2 class="font-bold">Daftar Hari Libur</h2>
                <p class="mt-1 text-xs text-slate-500">Menampilkan hari libur nasional tahun {{ $tahun }}.</p>
            </div>
            <form method="GET" action="{{ route('admin_hr.hari_libur.index') }}" class="flex items-end gap-2">
                <div class="flex flex-col gap-1.5">
                    <label for="tahun" class="text-xs font-semibold text-slate-500">Tahun</label>
                    <input id="tahun" name="tahun" type="number" min="2000" max="2100" value="{{ $tahun }}" class="w-28 rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950">
                </div>
                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900">Tampilkan</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950/50">
                    <tr>
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Nama Hari Libur</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($hariLibur as $libur)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold">{{ $libur->tanggal->translatedFormat('d F Y') }}</td>
                            <td class="px-5 py-4">{{ $libur->nama }}</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $libur->aktif,
                                    'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' => ! $libur->aktif,
                                ])>{{ $libur->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('admin_hr.hari_libur.edit', $libur) }}" class="font-semibold text-emerald-700 dark:text-emerald-400">Ubah</a>
                                    <form method="POST" action="{{ route('admin_hr.hari_libur.destroy', $libur) }}" onsubmit="return confirm('Hapus hari libur {{ $libur->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 dark:text-red-400">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-14 text-center text-slate-500">Belum ada hari libur nasional untuk tahun {{ $tahun }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-800">{{ $hariLibur->links() }}</div>
    </section>
@endsection
