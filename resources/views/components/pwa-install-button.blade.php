@props(['ringkas' => false])

@if ($ringkas)
    <button
        data-pwa-install
        data-pwa-install-compact
        type="button"
        hidden
        {{ $attributes->class('inline-flex size-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/15 dark:text-slate-300 dark:hover:bg-slate-800') }}
    >
        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/>
        </svg>
    </button>
@else
    <div data-pwa-install-container {{ $attributes->class('flex justify-center') }}>
        <button data-pwa-install-open type="button" class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-emerald-600/15 dark:text-emerald-300 dark:hover:bg-emerald-950">
            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/>
            </svg>
            Panduan pasang aplikasi
        </button>

        <p data-pwa-installed class="hidden text-center text-xs font-semibold text-emerald-700 dark:text-emerald-300" aria-live="polite">
            Medika Cuti sedang dibuka dalam aplikasi.
        </p>

        <dialog data-pwa-install-dialog class="m-auto w-[calc(100%_-_2rem)] max-w-sm overflow-hidden rounded-3xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                        <svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/>
                        </svg>
                    </span>
                    <button data-pwa-install-close type="button" aria-label="Tutup panduan pemasangan" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-emerald-600/15 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18"/></svg>
                    </button>
                </div>

                <h2 class="mt-5 text-xl font-bold tracking-tight">Pasang Medika Cuti</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Akses aplikasi lebih cepat langsung dari layar utama HP.</p>

                <ol data-pwa-install-android class="mt-5 space-y-3 text-sm text-slate-700 dark:text-slate-200">
                    <li class="flex gap-3"><span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">1</span><span>Tekan <strong>Pasang sekarang</strong> di bawah.</span></li>
                    <li class="flex gap-3"><span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">2</span><span>Pilih <strong>Instal</strong> pada konfirmasi browser.</span></li>
                    <li class="flex gap-3"><span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">3</span><span>Buka Medika Cuti dari ikon di layar utama.</span></li>
                </ol>

                <ol data-pwa-install-ios class="mt-5 hidden space-y-3 text-sm text-slate-700 dark:text-slate-200">
                    <li class="flex gap-3"><span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">1</span><span>Buka halaman ini menggunakan <strong>Safari</strong>.</span></li>
                    <li class="flex gap-3"><span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">2</span><span>Tekan tombol <strong>Bagikan</strong> pada Safari.</span></li>
                    <li class="flex gap-3"><span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">3</span><span>Pilih <strong>Tambahkan ke Layar Utama</strong>, lalu tekan <strong>Tambah</strong>.</span></li>
                </ol>

                <p data-pwa-install-status class="mt-4 text-xs leading-5 text-slate-500 dark:text-slate-400" aria-live="polite"></p>

                <div class="mt-6 flex flex-col gap-2">
                    <button data-pwa-install data-pwa-install-action type="button" class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">
                        Pasang sekarang
                    </button>
                    <button data-pwa-install-close type="button" class="inline-flex w-full items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-500/10 dark:text-slate-300 dark:hover:bg-slate-800">
                        Nanti saja
                    </button>
                </div>
            </div>
        </dialog>
    </div>
@endif
