@php
    $libur = $hariLibur ?? null;
@endphp

<div class="flex flex-col gap-7">
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="flex flex-col gap-2">
            <label for="tanggal" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Tanggal <span class="text-red-500">*</span></label>
            <input id="tanggal" name="tanggal" type="date" required value="{{ old('tanggal', $libur?->tanggal?->toDateString()) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('tanggal')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="nama" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Nama hari libur <span class="text-red-500">*</span></label>
            <input id="nama" name="nama" type="text" maxlength="150" required value="{{ old('nama', $libur?->nama) }}" placeholder="Contoh: Hari Raya Natal" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('nama')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <input type="hidden" name="aktif" value="0">
        <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-slate-200 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-950/50">
            <input name="aktif" type="checkbox" value="1" @checked((bool) old('aktif', $libur?->aktif ?? true)) class="mt-0.5 size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            <span>
                <span class="block text-sm font-semibold">Hari libur aktif</span>
                <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Tanggal aktif otomatis tidak dapat dipilih ketika karyawan mengajukan cuti.</span>
            </span>
        </label>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
        <a href="{{ route('admin_hr.hari_libur.index', ['tahun' => $libur?->tanggal?->year ?? now()->year]) }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Batal</a>
        <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">{{ $tombol }}</button>
    </div>
</div>
