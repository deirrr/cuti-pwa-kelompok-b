@extends('layouts.admin-hr')

@section('judul', 'Saldo Cuti Tahunan')

@section('konten-hr')
    <header>
        <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Cuti Karyawan</p>
        <h1 class="mt-3 text-3xl font-bold tracking-tight">Saldo Cuti Tahunan</h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Jatah awal setiap pegawai adalah 12 hari dan dapat disesuaikan oleh HR.</p>
    </header>

    @if (session('sukses'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('sukses') }}</div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form method="GET" class="grid gap-3 border-b border-slate-200 p-5 dark:border-slate-800 sm:grid-cols-[8rem_minmax(0,18rem)_auto]">
            <input name="tahun" type="number" min="2000" max="2100" value="{{ $tahun }}" aria-label="Tahun" class="rounded-xl border border-slate-300 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-950">
            <input name="cari" type="search" value="{{ $pencarian }}" placeholder="Cari nama atau NIK" aria-label="Cari pegawai" class="rounded-xl border border-slate-300 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-950">
            <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900">Tampilkan</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950/50">
                    <tr><th class="px-5 py-4">Pegawai</th><th class="px-5 py-4">Bagian</th><th class="px-5 py-4 text-center">Jatah</th><th class="px-5 py-4 text-center">Saldo</th><th class="px-5 py-4 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($pegawai as $item)
                        @php($saldo = $item->saldoCuti->first())
                        <tr>
                            <td class="px-5 py-4"><p class="font-semibold">{{ $item->nama }}</p><p class="text-xs text-slate-500">{{ $item->nomor_induk }}</p></td>
                            <td class="px-5 py-4">{{ $item->bagianOrganisasi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4 text-center">{{ $saldo?->jatah_awal ?? 12 }}</td>
                            <td class="px-5 py-4 text-center">{{ $saldo?->saldo_tersedia ?? 12 }}</td>
                            <td class="px-5 py-4 text-right"><a href="{{ route('admin_hr.saldo_cuti.edit', ['pegawai' => $item, 'tahun' => $tahun]) }}" class="font-semibold text-emerald-700 dark:text-emerald-400">Atur</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14 text-center text-slate-500">Pegawai tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-800">{{ $pegawai->links() }}</div>
    </section>
@endsection

