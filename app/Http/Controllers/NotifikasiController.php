<?php

namespace App\Http\Controllers;

use App\Enums\PeranPengguna;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $view = $request->user()->peran === PeranPengguna::AdminHr
            ? 'notifikasi.index-admin-hr'
            : 'notifikasi.index-pegawai';

        return view($view, [
            'notifikasi' => $request->user()
                ->notifications()
                ->latest('created_at')
                ->latest('id')
                ->paginate(15),
            'jumlahNotifikasiBelumDibaca' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function baca(Request $request, string $notifikasi): RedirectResponse
    {
        $notifikasiPengguna = $request->user()
            ->notifications()
            ->findOrFail($notifikasi);

        $notifikasiPengguna->markAsRead();

        return redirect()->route($this->routeTujuan($notifikasiPengguna->data['route_tujuan'] ?? null));
    }

    public function bacaSemua(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('sukses', 'Semua notifikasi telah ditandai dibaca.');
    }

    private function routeTujuan(mixed $route): string
    {
        return in_array($route, [
            'cuti.index',
            'persetujuan_atasan.index',
            'admin_hr.persetujuan.index',
        ], true) ? $route : 'notifikasi.index';
    }
}
