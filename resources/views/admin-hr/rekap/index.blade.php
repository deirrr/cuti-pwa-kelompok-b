@extends('layouts.admin-hr')

@section('judul', 'Rekap Cuti')

@section('konten-hr')
    <header>
        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <span>Admin HR</span>
            <span aria-hidden="true">/</span>
            <span class="font-medium text-slate-800 dark:text-slate-200">Rekap</span>
        </div>
        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Pusat rekap cuti</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-400">Pilih jenis laporan untuk meninjau saldo, status pengajuan, penggunaan cuti per bagian, atau tren cuti bulanan.</p>
    </header>

    @php
        $laporan = [
            [
                'judul' => 'Rekap Sisa Cuti',
                'deskripsi' => 'Pantau jatah awal, cuti terpakai, pengajuan yang masih menunggu, dan sisa saldo setiap karyawan.',
                'warna' => 'emerald',
                'ikon' => 'M12 3v18M3 12h18M5 5l14 14M19 5 5 19',
                'rute' => 'admin_hr.rekap.sisa_cuti',
            ],
            [
                'judul' => 'Rekap Cuti Berdasarkan Status',
                'deskripsi' => 'Tinjau pengajuan berdasarkan satu atau beberapa status dalam periode yang dipilih.',
                'warna' => 'blue',
                'ikon' => 'M4 6h16M4 12h10M4 18h7M18 15v6M15 18h6',
                'rute' => 'admin_hr.rekap.status_cuti',
            ],
            [
                'judul' => 'Penggunaan Cuti per Bagian',
                'deskripsi' => 'Bandingkan jumlah hari cuti dan jumlah karyawan yang mengambil cuti pada setiap bagian.',
                'warna' => 'violet',
                'ikon' => 'M4 21V10l8-5 8 5v11M8 21v-6h8v6M9 10h.01M15 10h.01',
                'rute' => 'admin_hr.rekap.penggunaan_bagian',
            ],
            [
                'judul' => 'Tren Cuti Bulanan',
                'deskripsi' => 'Lihat pola jumlah cuti setiap bulan untuk membantu evaluasi dan perencanaan operasional.',
                'warna' => 'amber',
                'ikon' => 'M3 3v18h18M7 16l4-5 3 3 5-7',
                'rute' => 'admin_hr.rekap.tren_bulanan',
            ],
        ];
    @endphp

    <section aria-label="Jenis laporan cuti" class="grid gap-5 md:grid-cols-2">
        @foreach ($laporan as $item)
            <article class="group flex min-h-64 flex-col justify-between overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 sm:p-7">
                <div>
                    <div class="flex items-start justify-between gap-4">
                        <span @class([
                            'flex size-12 shrink-0 items-center justify-center rounded-2xl',
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $item['warna'] === 'emerald',
                            'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' => $item['warna'] === 'blue',
                            'bg-violet-100 text-violet-700 dark:bg-violet-950 dark:text-violet-300' => $item['warna'] === 'violet',
                            'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' => $item['warna'] === 'amber',
                        ])>
                            <svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $item['ikon'] }}"/></svg>
                        </span>
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $item['rute'] !== null,
                            'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' => $item['rute'] === null,
                        ])>{{ $item['rute'] !== null ? 'Tersedia' : 'Tahap berikutnya' }}</span>
                    </div>
                    <h2 class="mt-6 text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $item['judul'] }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $item['deskripsi'] }}</p>
                </div>

                @if ($item['rute'] !== null)
                    <a href="{{ route($item['rute']) }}" class="mt-8 flex items-center justify-between gap-3 border-t border-slate-100 pt-5 text-sm font-semibold text-emerald-700 transition group-hover:text-emerald-800 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-600/15 dark:border-slate-800 dark:text-emerald-300 dark:group-hover:text-emerald-200">
                        Buka laporan
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                @else
                    <div class="mt-8 flex items-center gap-2 border-t border-slate-100 pt-5 text-sm font-semibold text-slate-500 dark:border-slate-800 dark:text-slate-400">
                        Filter periode, Excel, dan print akan tersedia pada detail laporan.
                    </div>
                @endif
            </article>
        @endforeach
    </section>
@endsection
