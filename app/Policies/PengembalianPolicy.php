<?php

namespace App\Policies;

use App\Models\Pengembalian;
use App\Models\User;

class PengembalianPolicy
{
    /**
     * Admin dan petugas boleh melihat daftar pengembalian.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPetugas();
    }

    /**
     * Admin dan petugas boleh melihat detail pengembalian.
     */
    public function view(User $user, Pengembalian $pengembalian): bool
    {
        return $user->isAdmin() || $user->isPetugas();
    }

    /**
     * Hanya admin yang boleh membuat data pengembalian melalui CRUD.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang boleh mengubah data pengembalian melalui CRUD.
     */
    public function update(User $user, Pengembalian $pengembalian): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang boleh menghapus data pengembalian.
     */
    public function delete(User $user, Pengembalian $pengembalian): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya petugas yang boleh menerima pengembalian.
     */
    public function accept(User $user, Pengembalian $pengembalian): bool
    {
        return $user->isPetugas()
            && $pengembalian->status === 'diajukan';
    }

    /**
     * Hanya petugas yang boleh menolak pengembalian.
     */
    public function reject(User $user, Pengembalian $pengembalian): bool
    {
        return $user->isPetugas()
            && $pengembalian->status === 'diajukan';
    }
}
