<div class="grid gap-8">
    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between gap-4">
                <label class="text-sm font-semibold">Tanggal cuti <span class="text-red-500">*</span></label>
                <span data-calendar-count class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">0 tanggal</span>
            </div>

            <div
                data-cuti-calendar
                data-selected='@json($tanggalTerpilih)'
                data-disabled='@json($hariLiburNasional)'
                data-max-selections="{{ $modeUbah ? 1 : 31 }}"
                data-min-date="{{ now()->toDateString() }}"
                class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-950/60">
                    <button data-calendar-previous type="button" aria-label="Bulan sebelumnya" class="flex size-9 items-center justify-center rounded-lg text-slate-600 hover:bg-white disabled:cursor-not-allowed disabled:opacity-30 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <p data-calendar-title class="font-bold"></p>
                    <button data-calendar-next type="button" aria-label="Bulan berikutnya" class="flex size-9 items-center justify-center rounded-lg text-slate-600 hover:bg-white dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
                <div class="grid grid-cols-7 border-b border-slate-100 px-3 py-2 text-center text-[11px] font-bold uppercase tracking-wide text-slate-400 dark:border-slate-800">
                    @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $hari)
                        <span>{{ $hari }}</span>
                    @endforeach
                </div>
                <div data-calendar-days class="grid grid-cols-7 gap-1 p-3"></div>
            </div>

            <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-2"><span class="size-3 rounded-full bg-emerald-600"></span>Dipilih</span>
                <span class="inline-flex items-center gap-2"><span class="size-3 rounded-full bg-red-100 ring-1 ring-red-200 dark:bg-red-950 dark:ring-red-900"></span>Minggu/libur nasional</span>
            </div>
            <div data-calendar-inputs></div>
            @error('tanggal_cuti')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            @error('tanggal_cuti.*')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <aside class="rounded-2xl bg-emerald-50 p-5 dark:bg-emerald-950/40">
            <h2 class="font-bold text-emerald-900 dark:text-emerald-200">Ketentuan tanggal</h2>
            <ul class="mt-4 flex list-disc flex-col gap-3 pl-5 text-sm leading-6 text-emerald-800 dark:text-emerald-300">
                <li>{{ $modeUbah ? 'Pilih satu tanggal pengganti.' : 'Anda dapat memilih tanggal secara acak.' }}</li>
                <li>Setiap tanggal menjadi satu pengajuan terpisah.</li>
                <li>Hari Minggu dan hari libur nasional tidak dapat dipilih.</li>
            </ul>
        </aside>
    </section>

    <div class="flex flex-col gap-2">
        <label for="alasan" class="text-sm font-semibold">Alasan cuti <span class="text-red-500">*</span></label>
        <textarea id="alasan" name="alasan" rows="5" maxlength="1000" required placeholder="Jelaskan alasan pengajuan cuti" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-950">{{ old('alasan', $pengajuanCuti?->alasan) }}</textarea>
        @error('alasan')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
        <a href="{{ route('cuti.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Batal</a>
        <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-700">{{ $modeUbah ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}</button>
    </div>
</div>
