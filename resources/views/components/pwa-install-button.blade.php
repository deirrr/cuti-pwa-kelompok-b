@props(['ringkas' => false])

<button
    data-pwa-install
    type="button"
    hidden
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition focus:outline-none focus:ring-4 focus:ring-emerald-600/15',
        'size-10 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-800' => $ringkas,
        'px-3 py-2 text-xs text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-950' => ! $ringkas,
    ]) }}
>
    <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/>
    </svg>
    @unless ($ringkas)
        <span>Pasang aplikasi</span>
    @endunless
</button>
