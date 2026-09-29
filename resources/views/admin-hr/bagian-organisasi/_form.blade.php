@php
    $bagian = $bagianOrganisasi ?? null;
@endphp

<div data-bagian-organisasi-form class="flex flex-col gap-7">
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="flex flex-col gap-2">
            <label for="unit_bisnis_id" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Unit bisnis <span class="text-red-500">*</span></label>
            <select data-unit-bisnis id="unit_bisnis_id" name="unit_bisnis_id" required class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                <option value="">Pilih unit bisnis</option>
                @foreach ($daftarUnitBisnis as $unit)
                    <option value="{{ $unit->getKey() }}" data-kategori="{{ $unit->kategori->value }}" @selected((int) old('unit_bisnis_id', $bagian?->unit_bisnis_id) === $unit->getKey())>{{ $unit->nama }}</option>
                @endforeach
            </select>
            @error('unit_bisnis_id')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="jenis" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Jenis <span class="text-red-500">*</span></label>
            <select data-jenis-bagian id="jenis" name="jenis" required class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                <option value="">Pilih jenis</option>
                @foreach (\App\Enums\JenisBagianOrganisasi::cases() as $jenis)
                    <option value="{{ $jenis->value }}" @selected(old('jenis', $bagian?->jenis?->value) === $jenis->value)>{{ $jenis->label() }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 dark:text-slate-400">Unit operasional menggunakan Bagian; HO menggunakan Direktorat atau Departemen.</p>
            @error('jenis')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div class="flex flex-col gap-2">
            <label for="kode" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Kode <span class="text-red-500">*</span></label>
            <input id="kode" name="kode" type="text" maxlength="20" required value="{{ old('kode', $bagian?->kode) }}" placeholder="Contoh: PENDAFTARAN" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm uppercase outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('kode')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="nama" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Nama <span class="text-red-500">*</span></label>
            <input id="nama" name="nama" type="text" maxlength="100" required value="{{ old('nama', $bagian?->nama) }}" placeholder="Contoh: Pendaftaran" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('nama')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="induk_id" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Induk struktur</label>
        <select data-induk-bagian id="induk_id" name="induk_id" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            <option value="">Langsung di bawah unit bisnis</option>
            @foreach ($daftarInduk as $induk)
                <option value="{{ $induk->getKey() }}" data-unit-bisnis-id="{{ $induk->unit_bisnis_id }}" @selected((int) old('induk_id', $bagian?->induk_id) === $induk->getKey())>{{ $induk->unitBisnis->kode }} — {{ $induk->jenis->label() }} {{ $induk->nama }}</option>
            @endforeach
        </select>
        <p class="text-xs text-slate-500 dark:text-slate-400">Contoh: Departemen Pelayanan dapat berada di bawah Direktorat Pelayanan.</p>
        @error('induk_id')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <input type="hidden" name="aktif" value="0">
        <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-slate-200 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-950/50">
            <input name="aktif" type="checkbox" value="1" @checked((bool) old('aktif', $bagian?->aktif ?? true)) class="mt-0.5 size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            <span><span class="block text-sm font-semibold">Bagian organisasi aktif</span><span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Data aktif dapat digunakan saat menyusun jabatan dan penugasan pegawai.</span></span>
        </label>
        @error('aktif')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
        <a href="{{ route('admin_hr.bagian_organisasi.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Batal</a>
        <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">{{ $tombol }}</button>
    </div>
</div>
