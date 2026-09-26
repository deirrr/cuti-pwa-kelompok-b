@extends('layouts.admin-hr')

@section('judul', 'Tambah Unit Bisnis')

@section('konten-hr')
    <header>
        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Unit Bisnis</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Tambah Unit Bisnis</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-400">Masukkan identitas unit yang akan digunakan dalam struktur organisasi.</p>
    </header>

    <form method="POST" action="{{ route('admin_hr.unit_bisnis.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
        @csrf
        @include('admin-hr.unit-bisnis._form', ['tombol' => 'Simpan Unit'])
    </form>
@endsection
