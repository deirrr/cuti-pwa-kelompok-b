<?php

namespace App\Notifications;

use App\Models\PengajuanCuti;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

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
        return ['database', WebPushChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->dataNotifikasi();
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $data = $this->dataNotifikasi();

        return (new WebPushMessage)
            ->title($data['judul'])
            ->body($data['pesan'])
            ->icon(asset('icons/icon-192.png'))
            ->badge(asset('icons/icon-192.png'))
            ->tag('pengajuan-cuti-'.$this->pengajuanCuti->getKey())
            ->data(['url' => route($this->routeTujuan, absolute: false)])
            ->action('Buka', 'buka_pengajuan')
            ->options(['TTL' => 3600]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dataNotifikasi(): array
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
