<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\User;

class ActivityLogger
{
    public static function log(
        string $aktivitas,
        string $deskripsi,
        ?User $user = null
    ): void {
        $user ??= auth()->user();

        LogAktivitas::create([
            'user_id' => $user?->id,
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'ip_address' => app()->runningInConsole()
                ? null
                : request()->ip(),
            'user_agent' => app()->runningInConsole()
                ? null
                : request()->userAgent(),
        ]);
    }
}
