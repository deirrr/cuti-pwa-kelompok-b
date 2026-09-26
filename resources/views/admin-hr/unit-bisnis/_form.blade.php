@php
    $unit = $unitBisnis ?? null;
@endphp

<div class="flex flex-col gap-7">
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="flex flex-col gap-2">
            <label for="kode" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Kode unit <span class="text-red-500">*</span></label>
            <input id="kode" name="kode" type="text" maxlength="20" required value="{{ old('kode', $unit?->kode) }}" placeholder="Contoh: KUMA" @class([
                'rounded-xl border bg-white px-4 py-3 text-sm uppercase outline-none transition placeholder:text-slate-400 focus:ring-4 dark:bg-slate-950',
                'border-red-400 focus:border-red-500 focus:ring-red-500/10' => $errors->has('kode'),
                'border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/10 dark:border-slate-700' => ! $errors->has('kode'),
            ])>
            <p class="text-xs text-slate-500 dark:text-slate-400">Singkatan unik untuk unit bisnis.</p>
            @error('kode')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="kategori" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Kategori <span class="text-red-500">*</span></label>
            <select id="kategori" name="kategori" required @class([
                'rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-4 dark:bg-slate-950',
                'border-red-400 focus:border-red-500 focus:ring-red-500/10' => $errors->has('kategori'),
                'border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/10 dark:border-slate-700' => ! $errors->has('kategori'),
            ])>
                <option value="">Pilih kategori</option>
                @foreach (\App\Enums\KategoriUnitBisnis::cases() as $kategori)
                    <option value="{{ $kategori->value }}" @selected(old('kategori', $unit?->kategori?->value) === $kategori->value)>{{ $kategori->label() }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pilih Head Office atau unit operasional.</p>
            @error('kategori')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="nama" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Nama unit <span class="text-red-500">*</span></label>
        <input id="nama" name="nama" type="text" maxlength="150" required value="{{ old('nama', $unit?->nama) }}" placeholder="Nama lengkap unit bisnis" @class([
            'rounded-xl border bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 dark:bg-slate-950',
            'border-red-400 focus:border-red-500 focus:ring-red-500/10' => $errors->has('nama'),
            'border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/10 dark:border-slate-700' => ! $errors->has('nama'),
        ])>
        <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan nama resmi yang mudah dikenali karyawan.</p>
        @error('nama')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <input type="hidden" name="aktif" value="0">
        <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-slate-200 bg-slate-50/60 p-4 transition hover:border-emerald-300 dark:border-slate-800 dark:bg-slate-950/50 dark:hover:border-emerald-800">
            <input name="aktif" type="checkbox" value="1" @checked((bool) old('aktif', $unit?->aktif ?? true)) class="mt-0.5 size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900">
            <span>
                <span class="block text-sm font-semibold text-slate-800 dark:text-slate-100">Unit aktif</span>
                <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Unit aktif dapat dipilih saat menyusun departemen, jabatan, dan penugasan pegawai.</span>
            </span>
        </label>
        @error('aktif')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
        <a href="{{ route('admin_hr.unit_bisnis.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-500/10 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">
            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
            {{ $tombol }}
        </button>
    </div>
</div>
