<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Notifications\TransaksiNotification;

class PeminjamanService
{
    /**
     * Menyetujui peminjaman dan mengurangi stok alat.
     */
    public function approve(Peminjaman $peminjaman, User $petugas): Peminjaman
    {
        if (! $petugas->isPetugas()) {
            throw new AuthorizationException(
                'Hanya petugas yang boleh menyetujui peminjaman.'
            );
        }

        return DB::transaction(function () use ($peminjaman, $petugas) {

            // Kunci data peminjaman agar tidak diproses bersamaan.
            $peminjaman = Peminjaman::query()
                ->lockForUpdate()
                ->findOrFail($peminjaman->id);

            if ($peminjaman->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman ini sudah diproses sebelumnya.',
                ]);
            }

            $details = $peminjaman->details()->get();

            if ($details->isEmpty()) {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Peminjaman belum memiliki detail alat.',
                ]);
            }

            $alatIds = $details
                ->pluck('alat_id')
                ->unique()
                ->sort()
                ->values();

            // Kunci semua alat berdasarkan urutan ID untuk mengurangi risiko deadlock.
            $alats = Alat::query()
                ->whereIn('id', $alatIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Pastikan seluruh alat masih tersedia.
            foreach ($details as $detail) {
                $alat = $alats->get($detail->alat_id);

                if (! $alat) {
                    throw ValidationException::withMessages([
                        'alat' => 'Salah satu alat dalam peminjaman tidak ditemukan.',
                    ]);
                }

                if (! $alat->is_active) {
                    throw ValidationException::withMessages([
                        'stok' => "Alat {$alat->name} sedang tidak aktif.",
                    ]);
                }

                if ($detail->jumlah < 1 || $alat->stok < $detail->jumlah) {
                    throw ValidationException::withMessages([
                        'stok' => "Stok alat {$alat->name} tidak mencukupi. Stok tersedia: {$alat->stok}.",
                    ]);
                }
            }

            // Kurangi stok setelah semua alat lolos pemeriksaan.
            foreach ($details as $detail) {
                $alat = $alats->get($detail->alat_id);

                $alat->decrement('stok', $detail->jumlah);
            }

            // Simpan petugas yang menyetujui peminjaman.
            $peminjaman->update([
                'status' => 'dipinjam',
                'disetujui_oleh' => $petugas->id,
            ]);

            ActivityLogger::log(
                'peminjaman_disetujui',
                "Petugas menyetujui peminjaman {$peminjaman->pinjam_code}. Stok alat telah dikurangi.",
                $petugas
            );

            $peminjaman->user->notify(new TransaksiNotification(
    judul: 'Peminjaman Disetujui',
    pesan: 'Pengajuan peminjaman ' . $peminjaman->pinjam_code . ' telah disetujui oleh Petugas.',
    url: route('peminjaman.riwayat'),
    jenis: 'peminjaman'
));

            return $peminjaman->fresh(['user', 'details.alat']);
        });
    }

    /**
     * Menolak peminjaman tanpa mengurangi stok.
     */
    public function reject(
        Peminjaman $peminjaman,
        User $petugas,
        ?string $alasan = null
    ): Peminjaman {
        if (! $petugas->isPetugas()) {
            throw new AuthorizationException(
                'Hanya petugas yang boleh menolak peminjaman.'
            );
        }

        return DB::transaction(function () use ($peminjaman, $petugas, $alasan) {

            $peminjaman = Peminjaman::query()
                ->lockForUpdate()
                ->findOrFail($peminjaman->id);

            if ($peminjaman->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman ini sudah diproses sebelumnya.',
                ]);
            }

            $peminjaman->update([
                'status' => 'ditolak',
            ]);

            ActivityLogger::log(
                'peminjaman_ditolak',
                "Petugas menolak peminjaman {$peminjaman->pinjam_code}."
                    . ($alasan ? " Alasan: {$alasan}" : ''),
                $petugas
            );

            $peminjaman->user->notify(new TransaksiNotification(
    judul: 'Peminjaman Ditolak',
    pesan: 'Pengajuan peminjaman ' . $peminjaman->pinjam_code . ' ditolak oleh Petugas.',
    url: route('peminjaman.riwayat'),
    jenis: 'peminjaman'
));

            return $peminjaman->fresh(['user', 'details.alat']);
        });
    }
}
