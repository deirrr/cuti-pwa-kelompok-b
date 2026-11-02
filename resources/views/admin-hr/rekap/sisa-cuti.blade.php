@extends('layouts.admin-hr')

@section('judul', 'Rekap Sisa Cuti')

@section('konten-hr')
    <header class="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin_hr.rekap.index') }}" class="font-medium transition hover:text-emerald-700 dark:hover:text-emerald-300">Rekap</a>
                <span aria-hidden="true">/</span>
                <span class="font-medium text-slate-800 dark:text-slate-200">Sisa Cuti</span>
            </div>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Rekap sisa cuti</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-400">Bandingkan penggunaan cuti pada periode terpilih dengan saldo tahunan resmi setiap karyawan.</p>
        </div>
        <a href="{{ route('admin_hr.rekap.index') }}" class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-600/15 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Rekap
        </a>
    </header>

    @if ($errors->any())
        <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
            <p class="font-semibold">Filter laporan belum valid.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section aria-labelledby="filter-rekap" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6">
        <div>
            <h2 id="filter-rekap" class="text-base font-bold text-slate-900 dark:text-white">Filter laporan</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tanggal mulai dan selesai harus berada dalam tahun yang sama.</p>
        </div>

        <form method="GET" action="{{ route('admin_hr.rekap.sisa_cuti') }}" class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
            <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                Tanggal mulai
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $tanggalMulai) }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-normal text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </label>
            <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                Tanggal selesai
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $tanggalSelesai) }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-normal text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </label>
            <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                Bagian organisasi
                <select name="bagian_organisasi_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-normal text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <option value="">Semua bagian</option>
                    @foreach ($daftarBagian as $bagian)
                        <option value="{{ $bagian->getKey() }}" @selected((string) $bagianOrganisasiId === (string) $bagian->getKey())>{{ $bagian->nama }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                Status pegawai
                <select name="status_pegawai" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-normal text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <option value="semua" @selected($statusPegawai === 'semua')>Semua status</option>
                    <option value="aktif" @selected($statusPegawai === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($statusPegawai === 'nonaktif')>Nonaktif</option>
                </select>
            </label>
            <label class="flex flex-col gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300 md:col-span-2 xl:col-span-2">
                Cari karyawan
                <input type="search" name="cari" value="{{ $pencarian }}" placeholder="Nama atau NIK" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-normal text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </label>

            <div class="flex flex-wrap items-end gap-2 md:col-span-2 xl:col-span-6">
                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-600/20">Terapkan Filter</button>
                <a href="{{ route('admin_hr.rekap.sisa_cuti') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-600/15 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Reset</a>
            </div>
        </form>
    </section>

    @php
        $kartuRingkasan = [
            ['label' => 'Karyawan ditemukan', 'nilai' => $ringkasan['jumlah_karyawan'], 'warna' => 'blue'],
            ['label' => 'Total jatah awal', 'nilai' => $ringkasan['total_jatah_awal'], 'warna' => 'violet'],
            ['label' => 'Terpakai pada periode', 'nilai' => $ringkasan['total_cuti_disetujui_periode'], 'warna' => 'amber'],
            ['label' => 'Total saldo tersedia', 'nilai' => $ringkasan['total_saldo_tersedia'], 'warna' => 'emerald'],
        ];
    @endphp

    <section aria-label="Ringkasan sisa cuti" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kartuRingkasan as $kartu)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $kartu['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $kartu['nilai'] }}</p>
                    </div>
                    <span @class([
                        'size-3 rounded-full',
                        'bg-blue-500' => $kartu['warna'] === 'blue',
                        'bg-violet-500' => $kartu['warna'] === 'violet',
                        'bg-amber-500' => $kartu['warna'] === 'amber',
                        'bg-emerald-500' => $kartu['warna'] === 'emerald',
                    ])></span>
                </div>
            </article>
        @endforeach
    </section>

    <section aria-labelledby="tabel-rekap" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col justify-between gap-2 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center">
            <div>
                <h2 id="tabel-rekap" class="font-bold text-slate-900 dark:text-white">Saldo cuti karyawan tahun {{ $tahun }}</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Periode penggunaan {{ \Carbon\CarbonImmutable::parse($tanggalMulai)->translatedFormat('d M Y') }}–{{ \Carbon\CarbonImmutable::parse($tanggalSelesai)->translatedFormat('d M Y') }}</p>
            </div>
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $rekap->count() }} karyawan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/60 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-semibold">Karyawan</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Bagian</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold">Jatah Tahun</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold">Terpakai Periode</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold">Terpakai Tahun</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold">Menunggu Periode</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold">Sisa Saat Ini</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold">Saldo Efektif</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($rekap as $item)
                        <tr class="align-top text-slate-700 dark:text-slate-300">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $item['nama'] }}</div>
                                <div class="mt-1 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <span>{{ $item['nomor_induk'] }}</span>
                                    <span @class([
                                        'rounded-full px-2 py-0.5 font-semibold',
                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $item['aktif'],
                                        'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' => ! $item['aktif'],
                                    ])>{{ $item['aktif'] ? 'Aktif' : 'Nonaktif' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">{{ $item['bagian'] }}</td>
                            @if ($item['saldo_diterbitkan'])
                                <td class="px-4 py-4 text-center font-semibold">{{ $item['jatah_awal'] }}</td>
                                <td class="px-4 py-4 text-center">{{ $item['cuti_disetujui_periode'] }}</td>
                                <td class="px-4 py-4 text-center">{{ $item['cuti_disetujui_tahun'] }}</td>
                                <td class="px-4 py-4 text-center">{{ $item['cuti_menunggu_periode'] }}</td>
                                <td class="px-4 py-4 text-center font-semibold text-emerald-700 dark:text-emerald-300">{{ $item['saldo_tersedia'] }}</td>
                                <td class="px-4 py-4 text-center font-bold text-slate-900 dark:text-white">{{ $item['saldo_efektif'] }}</td>
                            @else
                                <td colspan="6" class="px-4 py-4 text-center">
                                    <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300">Saldo {{ $tahun }} belum diterbitkan</span>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">Tidak ada karyawan yang sesuai dengan filter laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <aside class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-800 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-300">
        <span class="font-semibold">Catatan:</span> saldo efektif merupakan perkiraan sisa saldo setelah pengajuan yang masih menunggu pada periode terpilih ikut diperhitungkan. Saldo resmi tetap mengikuti kolom Sisa Saat Ini.
    </aside>
@endsection
