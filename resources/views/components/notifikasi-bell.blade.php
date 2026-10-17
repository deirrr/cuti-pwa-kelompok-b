@props(['jumlah' => 0])

<a
    href="{{ route('notifikasi.index') }}"
    aria-label="Notifikasi{{ $jumlah > 0 ? ' ('.$jumlah.' belum dibaca)' : '' }}"
    @class([
        'relative flex size-10 items-center justify-center rounded-full transition focus:outline-none focus:ring-4 focus:ring-emerald-600/10',
        'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => request()->routeIs('notifikasi.*'),
        'text-slate-600 hover:bg-slate-100 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-800' => ! request()->routeIs('notifikasi.*'),
    ])
>
    <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>
    @if ($jumlah > 0)
        <span class="absolute -right-1 -top-1 flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold leading-5 text-white ring-2 ring-white dark:ring-slate-900">
            {{ $jumlah > 99 ? '99+' : $jumlah }}
        </span>
    @endif
</a>
