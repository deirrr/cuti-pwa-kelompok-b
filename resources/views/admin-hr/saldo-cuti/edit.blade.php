@extends('layouts.admin-hr')

@section('judul', 'Atur Saldo Cuti')

@section('konten-hr')
    <header>
        <a href="{{ route('admin_hr.saldo_cuti.index', ['tahun' => $tahun]) }}" class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Saldo Cuti Tahunan</a>
        <h1 class="mt-3 text-3xl font-bold tracking-tight">Atur jatah {{ $pegawai->nama }}</h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">{{ $pegawai->nomor_induk }} · Tahun {{ $tahun }}</p>
    </header>

    <form method="POST" action="{{ route('admin_hr.saldo_cuti.update', $pegawai) }}" class="flex max-w-2xl flex-col gap-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @csrf
        @method('PUT')
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="flex flex-col gap-2">
                <label for="jatah_awal" class="text-sm font-semibold">Jatah tahunan</label>
                <input id="jatah_awal" name="jatah_awal" type="number" min="0" max="365" required value="{{ old('jatah_awal', $saldoCuti->jatah_awal) }}" class="rounded-xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950">
                @error('jatah_awal')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-col gap-2">
                <label for="saldo_tersedia" class="text-sm font-semibold">Saldo tersedia</label>
                <input id="saldo_tersedia" name="saldo_tersedia" type="number" min="0" max="365" required value="{{ old('saldo_tersedia', $saldoCuti->saldo_tersedia) }}" class="rounded-xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950">
                @error('saldo_tersedia')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <label for="catatan" class="text-sm font-semibold">Catatan</label>
            <textarea id="catatan" name="catatan" maxlength="255" rows="3" class="rounded-xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950">{{ old('catatan', $saldoCuti->catatan) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin_hr.saldo_cuti.index', ['tahun' => $tahun]) }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold dark:border-slate-700">Batal</a>
            <button class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
        </div>
    </form>
@endsection

