@extends('layouts.pegawai')

@section('judul', 'Ajukan Cuti')

@section('konten-pegawai')
    <header>
        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Cuti Saya / Pengajuan baru</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Ajukan Cuti Tahunan</h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Saldo yang dapat diajukan tahun ini: <strong>{{ $saldoTersedia }} hari</strong>.</p>
    </header>

    <form method="POST" action="{{ route('cuti.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
        @csrf
        @include('cuti._form', ['modeUbah' => false, 'pengajuanCuti' => null])
    </form>
@endsection
