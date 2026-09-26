@php
    $unit = $unitBisnis ?? null;
@endphp

<div class="flex flex-col gap-6">
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="flex flex-col gap-2">
            <label for="kode" class="text-sm font-medium">Kode unit</label>
            <input id="kode" name="kode" type="text" maxlength="20" required value="{{ old('kode', $unit?->kode) }}" placeholder="Contoh: KUMA" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm uppercase outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950">
            @error('kode')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="kategori" class="text-sm font-medium">Kategori</label>
            <select id="kategori" name="kategori" required class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950">
                <option value="">Pilih kategori</option>
                @foreach (\App\Enums\KategoriUnitBisnis::cases() as $kategori)
                    <option value="{{ $kategori->value }}" @selected(old('kategori', $unit?->kategori?->value) === $kategori->value)>{{ $kategori->label() }}</option>
                @endforeach
            </select>
            @error('kategori')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="nama" class="text-sm font-medium">Nama unit</label>
        <input id="nama" name="nama" type="text" maxlength="150" required value="{{ old('nama', $unit?->nama) }}" placeholder="Nama lengkap unit bisnis" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950">
        @error('nama')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <input type="hidden" name="aktif" value="0">
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 dark:border-slate-800">
            <input name="aktif" type="checkbox" value="1" @checked((bool) old('aktif', $unit?->aktif ?? true)) class="mt-0.5 size-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
            <span>
                <span class="block text-sm font-medium">Unit aktif</span>
                <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Unit aktif dapat digunakan saat menyusun struktur organisasi.</span>
            </span>
        </label>
        @error('aktif')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
        <a href="{{ route('admin_hr.unit_bisnis.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Batal</a>
        <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">{{ $tombol }}</button>
    </div>
</div>
