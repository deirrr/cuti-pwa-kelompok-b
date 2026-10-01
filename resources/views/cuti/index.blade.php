@extends('layouts.pegawai')

@section('judul', 'Cuti Saya')

@section('konten-pegawai')
    <header class="flex items-center justify-between gap-3 sm:items-end">
        <div class="min-w-0">
            <p class="hidden text-sm font-semibold text-emerald-700 dark:text-emerald-400 sm:block">Pengajuan pribadi</p>
            <h1 class="text-2xl font-bold tracking-tight sm:mt-2 sm:text-3xl">Cuti Saya</h1>
            <p class="mt-3 hidden text-sm text-slate-600 dark:text-slate-400 sm:block">Setiap baris mewakili satu tanggal pengajuan cuti.</p>
        </div>
        <a href="{{ route('cuti.create') }}" class="shrink-0 rounded-xl bg-emerald-600 px-4 py-2.5 text-center text-xs font-semibold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 sm:py-3 sm:text-sm">Ajukan Cuti</a>
    </header>

    @if (session('sukses'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('sukses') }}</div>
    @endif

    <section class="rounded-2xl bg-emerald-700 p-6 text-white shadow-lg shadow-emerald-900/10">
        <p class="text-sm text-emerald-100">Saldo cuti tersedia {{ $tahun }}</p>
        <p class="mt-2 text-4xl font-bold">{{ $saldoTersedia }} <span class="text-lg font-semibold text-emerald-100">hari</span></p>
    </section>

    <section class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <form method="GET" action="{{ route('cuti.index') }}" class="flex flex-col gap-1.5">
                <label for="tahun" class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tahun</label>
                @if ($statusTerpilih !== null)
                    <input type="hidden" name="status" value="{{ $statusTerpilih }}">
                @endif
                <select id="tahun" name="tahun" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950 sm:w-32">
                    @foreach ($tahunTersedia as $pilihanTahun)
                        <option value="{{ $pilihanTahun }}" @selected($pilihanTahun === $tahun)>{{ $pilihanTahun }}</option>
                    @endforeach
                </select>
            </form>

            <div class="min-w-0 flex-1">
                <p class="mb-1.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</p>
                <div data-status-scroller class="flex min-w-0 items-center gap-2">
                    <button data-status-scroll-previous type="button" aria-label="Geser status ke kiri" class="hidden size-8 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-30 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <div data-status-scroll-track class="flex min-w-0 flex-1 flex-nowrap gap-2 overflow-x-auto pb-1 overscroll-x-contain scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach ($pilihanStatus as $pilihan)
                            @php($aktif = $statusTerpilih === $pilihan['nilai'])
                            <a href="{{ route('cuti.index', array_filter(['tahun' => $tahun, 'status' => $aktif ? null : $pilihan['nilai']])) }}" aria-pressed="{{ $aktif ? 'true' : 'false' }}" @class([
                                'inline-flex shrink-0 items-center gap-2 rounded-full border px-3 py-2 text-xs font-semibold transition focus:outline-none focus:ring-4',
                                'border-amber-300 bg-amber-100 text-amber-800 focus:ring-amber-500/15 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-300' => $aktif && in_array($pilihan['nilai'], [\App\Enums\StatusPengajuanCuti::MenungguAtasan->value, \App\Enums\StatusPengajuanCuti::MenungguHr->value], true),
                                'border-emerald-300 bg-emerald-100 text-emerald-800 focus:ring-emerald-500/15 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' => $aktif && $pilihan['nilai'] === \App\Enums\StatusPengajuanCuti::Disetujui->value,
                                'border-red-300 bg-red-100 text-red-800 focus:ring-red-500/15 dark:border-red-800 dark:bg-red-950 dark:text-red-300' => $aktif && $pilihan['nilai'] === \App\Enums\StatusPengajuanCuti::Ditolak->value,
                                'border-slate-400 bg-slate-200 text-slate-800 focus:ring-slate-500/15 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200' => $aktif && $pilihan['nilai'] === \App\Enums\StatusPengajuanCuti::Dibatalkan->value,
                                'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50 focus:ring-slate-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-800' => ! $aktif,
                            ])>
                                {{ $pilihan['label'] }}
                                <span @class([
                                    'rounded-full px-1.5 py-0.5 text-[10px]',
                                    'bg-white/70 dark:bg-black/20' => $aktif,
                                    'bg-slate-100 dark:bg-slate-800' => ! $aktif,
                                ])>{{ $pilihan['jumlah'] }}</span>
                            </a>
                        @endforeach
                    </div>
                    <button data-status-scroll-next type="button" aria-label="Geser status ke kanan" class="hidden size-8 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-30 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="w-full text-left text-sm">
            <thead class="hidden bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/60 md:table-header-group">
                <tr><th class="px-5 py-4">Nomor</th><th class="px-5 py-4">Tanggal</th><th class="px-5 py-4">Alasan</th><th class="px-5 py-4">Status</th><th class="px-5 py-4 text-right">Aksi</th></tr>
            </thead>
            <tbody class="block divide-y divide-slate-100 dark:divide-slate-800 md:table-row-group">
                @forelse ($pengajuan as $item)
                    @php($dapatDiubah = in_array($item->status, [\App\Enums\StatusPengajuanCuti::MenungguAtasan, \App\Enums\StatusPengajuanCuti::MenungguHr], true) && $item->persetujuan->isEmpty())
                    <tr class="grid gap-4 p-5 md:table-row md:p-0">
                        <td class="block min-w-0 md:table-cell md:px-5 md:py-4">
                            <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400 md:hidden">Nomor</span>
                            <span class="block break-all font-semibold md:break-normal">{{ $item->nomor_pengajuan }}</span>
                        </td>
                        <td class="block md:table-cell md:whitespace-nowrap md:px-5 md:py-4">
                            <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400 md:hidden">Tanggal</span>
                            {{ $item->tanggal_cuti->translatedFormat('d F Y') }}
                        </td>
                        <td class="block min-w-0 text-slate-600 dark:text-slate-300 md:table-cell md:max-w-xs md:px-5 md:py-4">
                            <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400 md:hidden">Alasan</span>
                            <p class="break-words md:line-clamp-2">{{ $item->alasan }}</p>
                        </td>
                        <td class="block md:table-cell md:px-5 md:py-4">
                            <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400 md:hidden">Status</span>
                            <span @class([
                                'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' => in_array($item->status, [\App\Enums\StatusPengajuanCuti::MenungguAtasan, \App\Enums\StatusPengajuanCuti::MenungguHr], true),
                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' => $item->status === \App\Enums\StatusPengajuanCuti::Disetujui,
                                'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' => $item->status === \App\Enums\StatusPengajuanCuti::Ditolak,
                                'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' => in_array($item->status, [\App\Enums\StatusPengajuanCuti::Draf, \App\Enums\StatusPengajuanCuti::Dibatalkan], true),
                            ])>{{ $item->status->label() }}</span>
                        </td>
                        <td class="block md:table-cell md:px-5 md:py-4">
                            <span class="mb-2 block text-[11px] font-bold uppercase tracking-wide text-slate-400 md:hidden">Aksi</span>
                            @if ($dapatDiubah)
                                <div class="flex gap-2 md:justify-end">
                                    <a href="{{ route('cuti.edit', $item) }}" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-center text-xs font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800 md:flex-none">Ubah</a>
                                    <form method="POST" action="{{ route('cuti.cancel', $item) }}" class="flex-1 md:flex-none" onsubmit="return confirm('Batalkan pengajuan tanggal {{ $item->tanggal_cuti->translatedFormat('d F Y') }}?')">
                                        @csrf
                                        <button type="submit" class="w-full rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950">Batalkan</button>
                                    </form>
                                </div>
                            @else
                                <span class="block text-xs text-slate-400 md:text-right">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="block md:table-row">
                        <td colspan="5" class="block px-6 py-14 text-center md:table-cell">
                            <p class="font-semibold">Tidak ada pengajuan pada pilihan ini</p>
                            <p class="mt-2 text-sm text-slate-500">Pilih tahun atau status lain, atau buat pengajuan cuti baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
