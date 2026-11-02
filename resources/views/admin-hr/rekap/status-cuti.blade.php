@extends('layouts.admin-hr')

@section('judul', 'Rekap Cuti Berdasarkan Status')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin_hr.rekap.index') }}" class="font-medium hover:text-emerald-700 dark:hover:text-emerald-300">Rekap</a><span>/</span><span class="font-medium text-slate-800 dark:text-slate-200">Status Cuti</span>
            </div>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Rekap cuti berdasarkan status</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-400">Tinjau detail pengajuan dengan memilih satu atau beberapa status sekaligus.</p>
        </div>
        <div class="flex flex-wrap gap-2 print:hidden"><a href="{{ route('admin_hr.rekap.index') }}" class="inline-flex w-fit items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">← Kembali</a></div>
    </header>

    @if ($errors->any())
        <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
            <p class="font-semibold">Filter laporan belum valid.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section aria-labelledby="filter-status" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm print:hidden dark:border-slate-800 dark:bg-slate-900 sm:p-6">
        <h2 id="filter-status" class="text-base font-bold text-slate-900 dark:text-white">Filter laporan</h2>
        <form method="GET" action="{{ route('admin_hr.rekap.status_cuti') }}" class="mt-5 space-y-5">
            <input type="hidden" name="filter" value="1">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Tanggal mulai<input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $tanggalMulai) }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-normal dark:border-slate-700 dark:bg-slate-950 dark:text-white"></label>
                <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Tanggal selesai<input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $tanggalSelesai) }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-normal dark:border-slate-700 dark:bg-slate-950 dark:text-white"></label>
                <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Bagian organisasi<select name="bagian_organisasi_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-normal dark:border-slate-700 dark:bg-slate-950 dark:text-white"><option value="">Semua bagian</option>@foreach ($daftarBagian as $bagian)<option value="{{ $bagian->getKey() }}" @selected((string) $bagianOrganisasiId === (string) $bagian->getKey())>{{ $bagian->nama }}</option>@endforeach</select></label>
                <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Cari pengajuan<input type="search" name="cari" value="{{ $pencarian }}" placeholder="Nomor, nama, atau NIK" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-normal placeholder:text-slate-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white"></label>
            </div>
            <fieldset>
                <legend class="text-sm font-semibold text-slate-700 dark:text-slate-300">Status pengajuan</legend>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($statusTersedia as $status)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 has-checked:border-emerald-500 has-checked:bg-emerald-50 has-checked:text-emerald-800 dark:border-slate-700 dark:text-slate-300 dark:has-checked:bg-emerald-950/50 dark:has-checked:text-emerald-300">
                            <input type="checkbox" name="status[]" value="{{ $status->value }}" @checked(in_array($status->value, old('status', $statusDipilih), true)) class="accent-emerald-600">{{ $status->label() }}
                        </label>
                    @endforeach
                </div>
            </fieldset>
            @include('admin-hr.rekap.partials.aksi-laporan', ['namaRute' => 'admin_hr.rekap.status_cuti'])
        </form>
    </section>

    <section aria-label="Ringkasan status" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($statusTersedia as $status)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $status->label() }}</p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $ringkasanStatus[$status->value] }}</p>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h2 class="font-bold text-slate-900 dark:text-white">Detail pengajuan</h2><p class="mt-1 text-xs text-slate-500">{{ \Carbon\CarbonImmutable::parse($tanggalMulai)->translatedFormat('d M Y') }}–{{ \Carbon\CarbonImmutable::parse($tanggalSelesai)->translatedFormat('d M Y') }} · {{ $pengajuan->count() }} data</p></div>
        <div class="overflow-x-auto print:overflow-visible">
            <table class="w-full min-w-[900px] text-left text-sm print:min-w-0 print:text-[10px]">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/60"><tr><th class="px-5 py-3">Nomor</th><th class="px-5 py-3">Karyawan</th><th class="px-5 py-3">Bagian</th><th class="px-5 py-3">Tanggal cuti</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Diajukan</th></tr></thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($pengajuan as $item)
                        <tr class="text-slate-700 dark:text-slate-300"><td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">{{ $item->nomor_pengajuan }}</td><td class="px-5 py-4"><div class="font-semibold">{{ $item->pegawai->nama }}</div><div class="text-xs text-slate-500">{{ $item->pegawai->nomor_induk }}</div></td><td class="px-5 py-4">{{ $item->pegawai->bagianOrganisasi->nama }}</td><td class="px-5 py-4">{{ $item->tanggal_cuti->translatedFormat('d M Y') }}</td><td class="px-5 py-4"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold dark:bg-slate-800">{{ $item->status->label() }}</span></td><td class="px-5 py-4">{{ $item->diajukan_pada?->translatedFormat('d M Y H:i') ?? '—' }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">Tidak ada pengajuan yang sesuai dengan filter laporan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
