<section class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Pusat informasi</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 dark:text-white">Notifikasi</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Pantau pengajuan baru dan perubahan status cuti.</p>
        </div>

        @if ($jumlahNotifikasiBelumDibaca > 0)
            <form method="POST" action="{{ route('notifikasi.baca_semua') }}">
                @csrf
                <button type="submit" class="rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-emerald-600/10 dark:border-emerald-900 dark:bg-slate-900 dark:text-emerald-300 dark:hover:bg-emerald-950">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    @if (session('sukses'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
            {{ session('sukses') }}
        </div>
    @endif

    <div
        class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between"
        data-push-notifications
        data-vapid-public-key="{{ config('webpush.vapid.public_key') }}"
        data-subscribe-url="{{ route('notifikasi_push.store') }}"
        data-unsubscribe-url="{{ route('notifikasi_push.destroy') }}"
    >
        <div class="flex min-w-0 gap-4">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </span>
            <div>
                <h2 class="font-semibold text-slate-950 dark:text-white">Notifikasi perangkat</h2>
                <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400" data-push-status aria-live="polite">
                    Memeriksa dukungan notifikasi pada perangkat ini...
                </p>
            </div>
        </div>
        <button
            type="button"
            class="shrink-0 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-600/20 disabled:cursor-not-allowed disabled:opacity-50"
            data-push-toggle
            disabled
        >
            Aktifkan notifikasi
        </button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @forelse ($notifikasi as $item)
            <article @class([
                'flex flex-col gap-4 border-b border-slate-100 p-5 last:border-b-0 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between',
                'bg-emerald-50/60 dark:bg-emerald-950/20' => $item->unread(),
            ])>
                <div class="flex min-w-0 gap-4">
                    <span @class([
                        'mt-1 size-2.5 shrink-0 rounded-full',
                        'bg-emerald-500' => $item->unread(),
                        'bg-slate-300 dark:bg-slate-700' => $item->read(),
                    ])></span>
                    <div class="min-w-0">
                        <h2 class="font-semibold text-slate-950 dark:text-white">{{ $item->data['judul'] ?? 'Pembaruan cuti' }}</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $item->data['pesan'] ?? 'Terdapat pembaruan pada pengajuan cuti.' }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ $item->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('notifikasi.baca', $item->id) }}" class="shrink-0 self-end sm:self-auto">
                    @csrf
                    <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-600/20">
                        {{ $item->unread() ? 'Buka' : 'Lihat' }}
                    </button>
                </form>
            </article>
        @empty
            <div class="px-6 py-16 text-center">
                <span class="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800">
                    <svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </span>
                <h2 class="mt-4 font-semibold text-slate-900 dark:text-white">Belum ada notifikasi</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pembaruan pengajuan cuti akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    @if ($notifikasi->hasPages())
        {{ $notifikasi->links() }}
    @endif
</section>
