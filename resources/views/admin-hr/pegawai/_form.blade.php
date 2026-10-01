@php
    $dataPegawai = $pegawai ?? null;
    $modeUbah = $dataPegawai !== null;
    $peranTerpilih = old('peran', $dataPegawai?->pengguna?->peran?->value ?? \App\Enums\PeranPengguna::Karyawan->value);
@endphp

<div class="flex flex-col gap-8">
    <section class="grid gap-6 md:grid-cols-2">
        <div class="flex flex-col gap-2">
            <label for="nomor_induk" class="text-sm font-semibold text-slate-700 dark:text-slate-200">NIK <span class="text-red-500">*</span></label>
            <input id="nomor_induk" name="nomor_induk" type="text" maxlength="30" required value="{{ old('nomor_induk', $dataPegawai?->nomor_induk) }}" placeholder="Contoh: PGW001" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm uppercase outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('nomor_induk')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="nama" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Nama lengkap <span class="text-red-500">*</span></label>
            <input id="nama" name="nama" type="text" maxlength="150" required value="{{ old('nama', $dataPegawai?->nama) }}" placeholder="Nama lengkap karyawan" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('nama')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="email" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Email <span class="text-red-500">*</span></label>
            <input id="email" name="email" type="email" maxlength="255" required autocomplete="email" value="{{ old('email', $dataPegawai?->pengguna?->email) }}" placeholder="nama@medika-antapani.test" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('email')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="tanggal_masuk" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Tanggal masuk</label>
            <input id="tanggal_masuk" name="tanggal_masuk" type="date" value="{{ old('tanggal_masuk', $dataPegawai?->tanggal_masuk?->toDateString()) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('tanggal_masuk')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="bagian_organisasi_id" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Bagian organisasi <span class="text-red-500">*</span></label>
            <select id="bagian_organisasi_id" name="bagian_organisasi_id" required class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                <option value="">Pilih bagian</option>
                @foreach ($daftarBagian as $bagian)
                    <option value="{{ $bagian->getKey() }}" @selected((string) old('bagian_organisasi_id', $dataPegawai?->bagian_organisasi_id) === (string) $bagian->getKey())>{{ $bagian->nama }}</option>
                @endforeach
            </select>
            @error('bagian_organisasi_id')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="peran" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Peran <span class="text-red-500">*</span></label>
            <select id="peran" name="peran" data-peran required class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                @foreach ($daftarPeran as $peran)
                    <option value="{{ $peran->value }}" @selected($peranTerpilih === $peran->value)>{{ $peran->label() }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500">Peran menentukan akses dan alur persetujuan cuti.</p>
            @error('peran')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div data-atasan-group class="flex flex-col gap-2">
            <label for="atasan_id" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Atasan langsung <span class="text-red-500">*</span></label>
            <select id="atasan_id" name="atasan_id" data-atasan class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                <option value="">Pilih Atasan</option>
                @foreach ($daftarAtasan as $atasan)
                    <option value="{{ $atasan->getKey() }}" @selected((string) old('atasan_id', $dataPegawai?->atasan_id) === (string) $atasan->getKey())>{{ $atasan->nama }} · {{ $atasan->bagianOrganisasi->nama }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500">Wajib untuk Staff. Pengajuan Staff akan dikirim kepada Atasan ini.</p>
            @error('atasan_id')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div data-jatah-group class="flex flex-col gap-2">
            <label for="jatah_cuti" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Jatah cuti <span class="text-red-500">*</span></label>
            <div class="relative">
                <input id="jatah_cuti" name="jatah_cuti" data-jatah type="number" min="0" max="365" value="{{ old('jatah_cuti', $dataPegawai?->jatah_cuti ?? 12) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-16 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs text-slate-500">hari</span>
            </div>
            <p class="text-xs text-slate-500">Jatah berlaku tetap sampai HR mengubahnya. Nilai awal 12 hari.</p>
            @error('jatah_cuti')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </section>

    <section class="grid gap-6 border-t border-slate-200 pt-8 dark:border-slate-800 md:grid-cols-2">
        <div class="flex flex-col gap-2">
            <label for="kata_sandi" class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $modeUbah ? 'Kata sandi baru' : 'Kata sandi awal' }} @unless($modeUbah)<span class="text-red-500">*</span>@endunless</label>
            <input id="kata_sandi" name="kata_sandi" type="password" minlength="8" @required(! $modeUbah) autocomplete="new-password" placeholder="Minimal 8 karakter" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
            @error('kata_sandi')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="kata_sandi_confirmation" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Konfirmasi kata sandi</label>
            <input id="kata_sandi_confirmation" name="kata_sandi_confirmation" type="password" minlength="8" @required(! $modeUbah) autocomplete="new-password" placeholder="Ulangi kata sandi" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">
        </div>
    </section>

    <div>
        <input type="hidden" name="aktif" value="0">
        <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-slate-200 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-950/50">
            <input name="aktif" type="checkbox" value="1" @checked((bool) old('aktif', $dataPegawai?->aktif ?? true)) class="mt-0.5 size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            <span><span class="block text-sm font-semibold">Akun aktif</span><span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Karyawan aktif dapat masuk dan menggunakan fitur sesuai perannya.</span></span>
        </label>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
        <a href="{{ route('admin_hr.pegawai.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Batal</a>
        <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">{{ $tombol }}</button>
    </div>
</div>
