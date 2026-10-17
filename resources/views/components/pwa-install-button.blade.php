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
    <div data-pwa-install-container {{ $attributes->class('rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 dark:border-emerald-900 dark:bg-emerald-950/30') }}>
        <div class="flex items-start gap-3">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 shadow-sm dark:bg-slate-900 dark:text-emerald-300">
                <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/>
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Pasang Medika Cuti</p>
                <p data-pwa-install-status class="mt-1 text-xs leading-5 text-slate-600 dark:text-slate-300" aria-live="polite">
                    Pasang di layar utama agar aplikasi lebih mudah dibuka.
                </p>
                <div data-pwa-installed class="mt-1 hidden" aria-live="polite">
                    <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Aplikasi sudah terpasang.</p>
                    <p class="mt-0.5 text-xs leading-5 text-slate-600 dark:text-slate-300">Buka Medika Cuti dari ikon pada layar utama.</p>
                </div>
            </div>
        </div>
        <button
            data-pwa-install
            type="button"
            class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-600/20"
        >
            Pasang aplikasi
        </button>
    </div>
@endif
