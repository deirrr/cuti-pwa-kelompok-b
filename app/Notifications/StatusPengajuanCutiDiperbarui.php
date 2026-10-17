<?php

namespace App\Notifications;

use App\Models\PengajuanCuti;
use Illuminate\Notifications\Notification;

class StatusPengajuanCutiDiperbarui extends Notification
{
    public function __construct(
        public PengajuanCuti $pengajuanCuti,
        public string $judul,
        public string $pesan,
        public string $routeTujuan,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'judul' => $this->judul,
            'pesan' => $this->pesan,
            'jenis' => 'status_diperbarui',
            'pengajuan_cuti_id' => $this->pengajuanCuti->getKey(),
            'route_tujuan' => $this->routeTujuan,
        ];
    }
}
