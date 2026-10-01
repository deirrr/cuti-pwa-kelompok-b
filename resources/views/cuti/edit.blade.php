@extends('layouts.pegawai')

@section('judul', 'Ubah Pengajuan Cuti')

@section('konten-pegawai')
    <header>
        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Cuti Saya / {{ $pengajuanCuti->nomor_pengajuan }}</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Ubah Pengajuan Cuti</h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Pengajuan hanya dapat diubah sebelum mendapat keputusan.</p>
    </header>

    <form method="POST" action="{{ route('cuti.update', $pengajuanCuti) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
        @csrf
        @method('PUT')
        @include('cuti._form', ['modeUbah' => true])
    </form>
@endsection
