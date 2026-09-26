@extends('layouts.admin-hr')

@section('judul', 'Tambah Unit Bisnis')

@section('konten-hr')
    <header>
        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('admin_hr.unit_bisnis.index') }}" class="hover:text-emerald-700 dark:hover:text-emerald-400">Unit Bisnis</a>
            <span aria-hidden="true">/</span>
            <span class="font-medium text-slate-800 dark:text-slate-200">Tambah</span>
        </div>
        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Tambah Unit Bisnis</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-400">Masukkan identitas unit yang akan digunakan dalam struktur organisasi.</p>
    </header>

    <form method="POST" action="{{ route('admin_hr.unit_bisnis.store') }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @csrf
        <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:px-8">
            <h2 class="font-bold text-slate-900 dark:text-white">Informasi Unit</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Kolom bertanda bintang wajib diisi.</p>
        </div>
        <div class="p-6 sm:p-8">
            @include('admin-hr.unit-bisnis._form', ['tombol' => 'Simpan Unit'])
        </div>
    </form>
@endsection
