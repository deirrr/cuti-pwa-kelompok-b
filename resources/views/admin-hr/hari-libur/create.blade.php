@extends('layouts.admin-hr')

@section('judul', 'Tambah Hari Libur Nasional')

@section('konten-hr')
    <header>
        <p class="text-sm text-slate-500 dark:text-slate-400">Hari Libur Nasional / Tambah</p>
        <h1 class="mt-3 text-3xl font-bold tracking-tight">Tambah Hari Libur Nasional</h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Tanggal yang aktif akan dinonaktifkan pada kalender pengajuan cuti.</p>
    </header>

    <form method="POST" action="{{ route('admin_hr.hari_libur.store') }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @csrf
        <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:px-8">
            <h2 class="font-bold">Informasi Hari Libur</h2>
            <p class="mt-1 text-xs text-slate-500">Kolom bertanda bintang wajib diisi.</p>
        </div>
        <div class="p-6 sm:p-8">@include('admin-hr.hari-libur._form', ['tombol' => 'Simpan Hari Libur'])</div>
    </form>
@endsection
