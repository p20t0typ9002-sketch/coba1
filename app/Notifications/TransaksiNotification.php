<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransaksiNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $judul,
        public string $pesan,
        public string $url,
        public string $jenis = 'transaksi'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'judul' => $this->judul,
            'pesan' => $this->pesan,
            'url' => $this->url,
            'jenis' => $this->jenis,
            'waktu' => now()->toDateTimeString(),
        ];
    }
}
