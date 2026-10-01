@extends('layouts.pegawai')

@section('judul', 'Cuti Saya')

@section('konten-pegawai')
    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Pengajuan pribadi</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">Cuti Saya</h1>
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Setiap baris mewakili satu tanggal pengajuan cuti.</p>
        </div>
        <a href="{{ route('cuti.create') }}" class="rounded-xl bg-emerald-600 px-4 py-3 text-center text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-700">Ajukan Cuti</a>
    </header>

    @if (session('sukses'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('sukses') }}</div>
    @endif

    <section class="rounded-2xl bg-emerald-700 p-6 text-white shadow-lg shadow-emerald-900/10">
        <p class="text-sm text-emerald-100">Saldo cuti tersedia {{ now()->year }}</p>
        <p class="mt-2 text-4xl font-bold">{{ $saldoTersedia }} <span class="text-lg font-semibold text-emerald-100">hari</span></p>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/60">
                    <tr><th class="px-5 py-4">Nomor</th><th class="px-5 py-4">Tanggal</th><th class="px-5 py-4">Alasan</th><th class="px-5 py-4">Status</th><th class="px-5 py-4 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($pengajuan as $item)
                        @php($dapatDiubah = in_array($item->status, [\App\Enums\StatusPengajuanCuti::MenungguAtasan, \App\Enums\StatusPengajuanCuti::MenungguHr], true) && $item->persetujuan->isEmpty())
                        <tr>
                            <td class="px-5 py-4 font-semibold">{{ $item->nomor_pengajuan }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->tanggal_cuti->translatedFormat('d F Y') }}</td>
                            <td class="max-w-xs px-5 py-4 text-slate-600 dark:text-slate-300"><p class="line-clamp-2">{{ $item->alasan }}</p></td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' => in_array($item->status, [\App\Enums\StatusPengajuanCuti::MenungguAtasan, \App\Enums\StatusPengajuanCuti::MenungguHr], true),
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' => $item->status === \App\Enums\StatusPengajuanCuti::Disetujui,
                                    'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' => $item->status === \App\Enums\StatusPengajuanCuti::Ditolak,
                                    'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' => in_array($item->status, [\App\Enums\StatusPengajuanCuti::Draf, \App\Enums\StatusPengajuanCuti::Dibatalkan], true),
                                ])>{{ $item->status->label() }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($dapatDiubah)
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('cuti.edit', $item) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Ubah</a>
                                        <form method="POST" action="{{ route('cuti.cancel', $item) }}" onsubmit="return confirm('Batalkan pengajuan tanggal {{ $item->tanggal_cuti->translatedFormat('d F Y') }}?')">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950">Batalkan</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="block text-right text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-14 text-center"><p class="font-semibold">Belum ada pengajuan cuti</p><p class="mt-2 text-sm text-slate-500">Klik “Ajukan Cuti” untuk membuat pengajuan pertama.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
