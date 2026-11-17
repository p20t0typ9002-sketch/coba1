<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    /**
     * Admin dan petugas boleh melihat daftar peminjaman.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPetugas();
    }

    /**
     * Admin dan petugas boleh melihat detail peminjaman.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        return $user->isAdmin() || $user->isPetugas();
    }

    /**
     * Hanya admin yang boleh membuat data peminjaman melalui CRUD.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang boleh mengubah data peminjaman melalui CRUD.
     */
    public function update(User $user, Peminjaman $peminjaman): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang boleh menghapus data peminjaman.
     */
    public function delete(User $user, Peminjaman $peminjaman): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya petugas yang boleh menyetujui peminjaman.
     */
    public function approve(User $user, Peminjaman $peminjaman): bool
    {
        return $user->isPetugas()
            && $peminjaman->status === 'menunggu';
    }

    /**
     * Hanya petugas yang boleh menolak peminjaman.
     */
    public function reject(User $user, Peminjaman $peminjaman): bool
    {
        return $user->isPetugas()
            && $peminjaman->status === 'menunggu';
    }
}
