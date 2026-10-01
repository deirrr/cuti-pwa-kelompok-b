@extends('layouts.pegawai')

@section('judul', 'Persetujuan Staff')

@section('konten-pegawai')
    <header>
        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Area Atasan</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Persetujuan Staff</h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Tinjau pengajuan Staff yang menjadikan Anda sebagai Atasan langsung.</p>
    </header>

    @if (session('sukses'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('sukses') }}</div>
    @endif

    <section class="flex flex-col gap-5">
        @forelse ($pengajuan as $item)
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="grid gap-6 p-5 lg:grid-cols-[minmax(0,1fr)_minmax(280px,0.7fr)] lg:p-6">
                    <div>
                        <div class="flex flex-col justify-between gap-3 sm:flex-row">
                            <div>
                                <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">{{ $item->nomor_pengajuan }}</p>
                                <h2 class="mt-2 text-lg font-bold">{{ $item->pegawai->nama }}</h2>
                                <p class="mt-1 text-sm text-slate-500">{{ $item->pegawai->bagianOrganisasi->nama }}</p>
                            </div>
                            <span class="h-fit w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950 dark:text-amber-300">Menunggu keputusan Anda</span>
                        </div>
                        <dl class="mt-5"><div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-950/60"><dt class="text-xs text-slate-500">Tanggal cuti</dt><dd class="mt-1 font-semibold">{{ $item->tanggal_cuti->translatedFormat('d F Y') }}</dd></div></dl>
                        <div class="mt-4"><p class="text-xs text-slate-500">Alasan</p><p class="mt-1 text-sm leading-6">{{ $item->alasan }}</p></div>
                    </div>

                    <form method="POST" action="{{ route('persetujuan_atasan.store', $item) }}" class="flex flex-col gap-4 rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label for="catatan-{{ $item->getKey() }}" class="text-sm font-semibold">Catatan keputusan</label>
                            <textarea id="catatan-{{ $item->getKey() }}" name="catatan" rows="4" maxlength="1000" placeholder="Wajib diisi jika ditolak" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button name="keputusan" value="ditolak" class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950">Tolak</button>
                            <button name="keputusan" value="disetujui" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Setujui</button>
                        </div>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center dark:border-slate-700 dark:bg-slate-900">
                <p class="font-semibold">Tidak ada pengajuan yang menunggu</p>
                <p class="mt-2 text-sm text-slate-500">Semua pengajuan Staff sudah diproses.</p>
            </div>
        @endforelse
    </section>
@endsection
