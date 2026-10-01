@extends('layouts.admin-hr')

@section('judul', 'Master Karyawan')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Klinik Utama Medika Antapani</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight">Master Karyawan</h1>
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Seluruh karyawan dikelompokkan berdasarkan bagian organisasi.</p>
        </div>
        <a href="{{ route('admin_hr.pegawai.create') }}" class="rounded-xl bg-emerald-600 px-4 py-3 text-center text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">Tambah Karyawan</a>
    </header>

    @if (session('sukses'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('sukses') }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
            <div>
                <h2 class="font-bold">{{ $jumlahKaryawan }} karyawan ditampilkan</h2>
                <p class="mt-1 text-xs text-slate-500">Jatah cuti melekat pada masing-masing karyawan.</p>
            </div>
            <form method="GET" action="{{ route('admin_hr.pegawai.index') }}" class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_160px_auto]">
                <input name="cari" type="search" value="{{ $pencarian }}" placeholder="Cari NIK, nama, atau email" class="min-w-0 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                <select name="peran" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Semua peran</option>
                    @foreach ($daftarPeran as $peran)
                        <option value="{{ $peran->value }}" @selected($filterPeran === $peran->value)>{{ $peran->label() }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900">Terapkan</button>
            </form>
        </div>
    </section>

    <section aria-label="Karyawan berdasarkan bagian organisasi" class="grid items-start gap-6 xl:grid-cols-2">
        @forelse ($bagianOrganisasi as $bagian)
            <article data-bagian-card="{{ $bagian->getKey() }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <header class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/50">
                    <div class="min-w-0">
                        <h2 class="truncate font-bold text-slate-900 dark:text-white">{{ $bagian->nama }}</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ $bagian->kode }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ $bagian->pegawai->count() }} karyawan</span>
                </header>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($bagian->pegawai as $item)
                        <div data-pegawai-id="{{ $item->getKey() }}" class="p-4 sm:p-5">
                            <div class="flex items-center gap-4">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ str($item->nama)->substr(0, 2)->upper() }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $item->nama }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ $item->nomor_induk }} · {{ $item->pengguna->email }}</p>
                                </div>
                                <span @class([
                                    'hidden shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold sm:inline-flex',
                                    'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' => $item->pengguna->peran === \App\Enums\PeranPengguna::Karyawan,
                                    'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' => $item->pengguna->peran === \App\Enums\PeranPengguna::Atasan,
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $item->pengguna->peran === \App\Enums\PeranPengguna::AdminHr,
                                ])>{{ $item->pengguna->peran->label() }}</span>
                                <button data-dialog-pegawai-open="dialog-pegawai-{{ $item->getKey() }}" type="button" aria-label="Lihat detail {{ $item->nama }}" aria-haspopup="dialog" class="flex size-10 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/10 dark:text-slate-400 dark:hover:bg-emerald-950 dark:hover:text-emerald-300">
                                    <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>

                            <dialog id="dialog-pegawai-{{ $item->getKey() }}" data-dialog-pegawai class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl bg-transparent p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 backdrop:backdrop-blur-sm dark:text-white">
                                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                                    <header class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ str($item->nama)->substr(0, 2)->upper() }}</span>
                                            <div class="min-w-0">
                                                <h3 class="truncate font-bold">{{ $item->nama }}</h3>
                                                <p class="mt-1 text-xs text-slate-500">Detail karyawan</p>
                                            </div>
                                        </div>
                                        <form method="dialog">
                                            <button type="submit" aria-label="Tutup detail karyawan" class="flex size-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                                                <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    </header>

                                    <dl class="grid gap-x-6 gap-y-5 p-5 text-sm sm:grid-cols-2">
                                        <div><dt class="text-xs text-slate-500">NIK</dt><dd class="mt-1 font-semibold">{{ $item->nomor_induk }}</dd></div>
                                        <div><dt class="text-xs text-slate-500">Peran</dt><dd class="mt-1 font-semibold">{{ $item->pengguna->peran->label() }}</dd></div>
                                        <div class="sm:col-span-2"><dt class="text-xs text-slate-500">Email</dt><dd class="mt-1 break-all font-semibold">{{ $item->pengguna->email }}</dd></div>
                                        <div class="sm:col-span-2"><dt class="text-xs text-slate-500">Bagian organisasi</dt><dd class="mt-1 font-semibold">{{ $bagian->nama }}</dd></div>
                                        <div><dt class="text-xs text-slate-500">Atasan langsung</dt><dd class="mt-1 font-semibold">{{ $item->atasan?->nama ?? '—' }}</dd></div>
                                        <div><dt class="text-xs text-slate-500">Jatah cuti</dt><dd class="mt-1 font-semibold">{{ $item->pengguna->peran === \App\Enums\PeranPengguna::AdminHr ? '—' : "{$item->jatah_cuti} hari" }}</dd></div>
                                        <div class="sm:col-span-2"><dt class="text-xs text-slate-500">Tanggal masuk</dt><dd class="mt-1 font-semibold">{{ $item->tanggal_masuk?->translatedFormat('d F Y') ?? '—' }}</dd></div>
                                    </dl>

                                    <footer class="flex justify-end border-t border-slate-200 px-5 py-4 dark:border-slate-800">
                                        <a href="{{ route('admin_hr.pegawai.edit', $item) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">
                                            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                            Ubah Data
                                        </a>
                                    </footer>
                                </div>
                            </dialog>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-slate-500">Belum ada karyawan pada bagian ini.</div>
                    @endforelse
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 xl:col-span-2">Tidak ada data karyawan yang sesuai dengan pencarian atau filter.</div>
        @endforelse
    </section>
@endsection
