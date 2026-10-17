<?php

namespace App\Notifications;

use App\Models\PengajuanCuti;
use Illuminate\Notifications\Notification;

class PengajuanCutiDiajukan extends Notification
{
    public function __construct(
        public PengajuanCuti $pengajuanCuti,
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
        $this->pengajuanCuti->loadMissing('pegawai');

        return [
            'judul' => 'Pengajuan cuti baru',
            'pesan' => sprintf(
                '%s mengajukan cuti untuk tanggal %s.',
                $this->pengajuanCuti->pegawai->nama,
                $this->pengajuanCuti->tanggal_cuti->format('d-m-Y'),
            ),
            'jenis' => 'pengajuan_baru',
            'pengajuan_cuti_id' => $this->pengajuanCuti->getKey(),
            'route_tujuan' => $this->routeTujuan,
        ];
    }
}
