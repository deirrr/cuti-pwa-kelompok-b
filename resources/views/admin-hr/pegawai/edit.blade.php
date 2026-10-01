@extends('layouts.admin-hr')

@section('judul', 'Ubah Karyawan')

@section('konten-hr')
    <header>
        <p class="text-sm text-slate-500 dark:text-slate-400">Master Karyawan / Ubah</p>
        <h1 class="mt-3 text-3xl font-bold tracking-tight">Ubah {{ $pegawai->nama }}</h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Perbarui akun, peran, atasan langsung, dan jatah cuti karyawan.</p>
    </header>

    <form method="POST" action="{{ route('admin_hr.pegawai.update', $pegawai) }}" data-pegawai-form class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @csrf
        @method('PUT')
        <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:px-8"><h2 class="font-bold">Informasi Karyawan</h2><p class="mt-1 text-xs text-slate-500">Kosongkan kata sandi jika tidak ingin mengubahnya.</p></div>
        <div class="p-6 sm:p-8">@include('admin-hr.pegawai._form', ['tombol' => 'Simpan Perubahan'])</div>
    </form>
@endsection
