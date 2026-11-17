<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        $notification->markAsRead();

        $url = $notification->data['url'] ?? '/';

        // Pastikan tujuan pengalihan merupakan URL internal.
        if (! is_string($url) || ! str_starts_with($url, '/')) {
            $url = '/';
        }

        return redirect($url);
    }

    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return back()->with(
            'success',
            'Semua notifikasi telah ditandai sudah dibaca.'
        );
    }
}
