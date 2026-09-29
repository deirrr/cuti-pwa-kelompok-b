@extends('layouts.admin-hr')

@section('judul', 'Ubah Bagian Organisasi')

@section('konten-hr')
    <header><p class="text-sm text-slate-500 dark:text-slate-400">Bagian Organisasi / Ubah</p><h1 class="mt-3 text-3xl font-bold tracking-tight">Ubah {{ $bagianOrganisasi->nama }}</h1><p class="mt-3 text-sm text-slate-600 dark:text-slate-400">Perubahan tidak menghapus hubungan organisasi yang sudah tersimpan.</p></header>
    <form method="POST" action="{{ route('admin_hr.bagian_organisasi.update', $bagianOrganisasi) }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @csrf
        @method('PUT')
        <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:px-8"><h2 class="font-bold">Informasi Struktur</h2><p class="mt-1 text-xs text-slate-500">Periksa kembali data sebelum menyimpan.</p></div>
        <div class="p-6 sm:p-8">@include('admin-hr.bagian-organisasi._form', ['tombol' => 'Simpan Perubahan'])</div>
    </form>
@endsection
