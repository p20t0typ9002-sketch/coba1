<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\LogsActivity;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'level',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, LogsActivity;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Pemeriksaan role
    public function isAdmin(): bool
    {
        return $this->level === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->level === 'petugas';
    }

    public function isPeminjam(): bool
    {
        return $this->level === 'peminjam';
    }

    // Alias untuk kompatibilitas kode lama Indotravi
    public function isUser(): bool
    {
        return $this->isPeminjam();
    }

    // Peminjaman yang diajukan oleh pengguna
    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }

    // Peminjaman yang disetujui oleh admin atau petugas
    public function peminjamanDisetujui(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'disetujui_oleh');
    }

    // Pengembalian yang diterima oleh petugas
    public function pengembalianDiterima(): HasMany
    {
        return $this->hasMany(Pengembalian::class, 'diterima_oleh');
    }

    // Riwayat aktivitas pengguna
    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'user_id');
    }

    // Hak akses Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isPetugas();
    }
}
