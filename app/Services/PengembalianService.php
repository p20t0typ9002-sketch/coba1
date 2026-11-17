<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Notifications\TransaksiNotification;

class PengembalianService
{
    public function accept(Pengembalian $pengembalian, User $petugas): Pengembalian
    {
        if (! $petugas->isPetugas()) {
            throw new AuthorizationException(
                'Hanya petugas yang dapat menerima pengembalian.'
            );
        }

        return DB::transaction(function () use ($pengembalian, $petugas) {
            // Kunci transaksi peminjaman agar proses stok tidak bertabrakan.
            $peminjaman = Peminjaman::query()
                ->lockForUpdate()
                ->findOrFail($pengembalian->peminjaman_id);

            $pengembalian = Pengembalian::query()
                ->lockForUpdate()
                ->findOrFail($pengembalian->id);

            if ($pengembalian->status !== 'diajukan') {
                throw ValidationException::withMessages([
                    'status' => 'Pengembalian ini sudah diproses sebelumnya.',
                ]);
            }

            if ($peminjaman->status !== 'dipinjam') {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Peminjaman ini tidak sedang berstatus dipinjam.',
                ]);
            }

            $detailPengembalians = $pengembalian->details()->get();
            $jumlahPerDetail = $detailPengembalians
                ->groupBy('detail_pinjam_id')
                ->map(fn ($items) => $items->sum('jumlah_kembali'));

            if ($detailPengembalians->isEmpty()) {
                throw ValidationException::withMessages([
                    'pengembalian' => 'Pengembalian belum memiliki detail alat.',
                ]);
            }

            $detailPinjamIds = $detailPengembalians
                ->pluck('detail_pinjam_id')
                ->unique()
                ->sort()
                ->values();

            // Kunci detail peminjaman.
            $detailPeminjamans = DetailPeminjaman::query()
                ->whereIn('id', $detailPinjamIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Kunci data alat.
            $alatIds = $detailPeminjamans
                ->pluck('alat_id')
                ->unique()
                ->sort()
                ->values();

            $alats = Alat::query()
                ->whereIn('id', $alatIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Validasi seluruh detail sebelum mengubah stok.
            foreach ($jumlahPerDetail as $detailPinjamId => $jumlahAkanDikembalikan) {
                $detailPinjam = $detailPeminjamans->get($detailPinjamId);

                if (! $detailPinjam || $detailPinjam->pinjam_id != $peminjaman->id) {
                    throw ValidationException::withMessages([
                        'detail' => 'Detail alat bukan bagian dari peminjaman ini.',
                    ]);
                }

                $jumlahSudahDikembalikan = $detailPinjam
                    ->detailPengembalians()
                    ->whereHas('pengembalian', function ($query) {
                        $query->where('status', 'diterima');
                    })
                ->sum('jumlah_kembali');

                $sisaPinjaman = $detailPinjam->jumlah - $jumlahSudahDikembalikan;

                if ($jumlahAkanDikembalikan < 1 || $jumlahAkanDikembalikan > $sisaPinjaman) {
                    throw ValidationException::withMessages([
                        'jumlah_kembali' => 'Jumlah pengembalian melebihi sisa pinjaman atau tidak valid.',
                    ]);
                }

                if (! $alats->has($detailPinjam->alat_id)) {
                    throw ValidationException::withMessages([
                        'alat' => 'Data alat tidak ditemukan.',
                    ]);
                }
            }

            // Tambahkan stok hanya untuk alat berkondisi baik.
            foreach ($detailPengembalians as $detailKembali) {
                $detailPinjam = $detailPeminjamans->get(
                    $detailKembali->detail_pinjam_id
                );

                if ($detailKembali->kondisi_alat === 'baik') {
                    $alat = $alats->get($detailPinjam->alat_id);

                    $alat->increment(
                        'stok',
                        $detailKembali->jumlah_kembali
                    );
                }
            }

            $pengembalian->update([
                'status' => 'diterima',
                'diterima_oleh' => $petugas->id,
            ]);

            // Periksa apakah seluruh jumlah pinjaman sudah tercatat kembali.
            $seluruhDetailPinjaman = $peminjaman->details()->get();

            $semuaSudahDiproses = $seluruhDetailPinjaman->every(
                function ($detailPinjam) {
                    $jumlahDikembalikan = $detailPinjam
                        ->detailPengembalians()
                        ->whereHas('pengembalian', function ($query) {
                            $query->where('status', 'diterima');
                        })
                        ->sum('jumlah_kembali');

                    return $jumlahDikembalikan >= $detailPinjam->jumlah;
                }
            );

            if ($semuaSudahDiproses) {
                $peminjaman->update([
                    'status' => 'selesai',
                ]);
            }

            ActivityLogger::log(
                'pengembalian_diterima',
                "Petugas menerima pengembalian {$peminjaman->pinjam_code}.",
                $petugas
            );

            $pengembalian->peminjaman->user->notify(new TransaksiNotification(
    judul: 'Pengembalian Diterima',
    pesan: 'Pengembalian alat untuk peminjaman '
        . $pengembalian->peminjaman->pinjam_code
        . ' telah diterima oleh Petugas.',
    url: route('pengembalian.index'),
    jenis: 'pengembalian'
));

            return $pengembalian->fresh([
                'peminjaman.user',
                'details.detailPeminjaman.alat',
            ]);
        });
    }

    public function reject(
        Pengembalian $pengembalian,
        User $petugas,
        ?string $alasan = null
    ): Pengembalian {
        if (! $petugas->isPetugas()) {
            throw new AuthorizationException(
                'Hanya petugas yang dapat menolak pengembalian.'
            );
        }

        return DB::transaction(function () use (
            $pengembalian,
            $petugas,
            $alasan
        ) {
            $peminjaman = Peminjaman::query()
                ->lockForUpdate()
                ->findOrFail($pengembalian->peminjaman_id);

            $pengembalian = Pengembalian::query()
                ->lockForUpdate()
                ->findOrFail($pengembalian->id);

            if ($pengembalian->status !== 'diajukan') {
                throw ValidationException::withMessages([
                    'status' => 'Pengembalian ini sudah diproses sebelumnya.',
                ]);
            }

            $catatan = $pengembalian->catatan;

            if ($alasan) {
                $catatan = trim(
                    ($catatan ? $catatan . "\n" : '')
                    . 'Alasan penolakan petugas: '
                    . $alasan
                );
            }

            $pengembalian->update([
                'status' => 'ditolak',
                'catatan' => $catatan,
            ]);

            ActivityLogger::log(
                'pengembalian_ditolak',
                "Petugas menolak pengembalian {$peminjaman->pinjam_code}."
                    . ($alasan ? " Alasan: {$alasan}" : ''),
                $petugas
            );

            $pengembalian->peminjaman->user->notify(new TransaksiNotification(
    judul: 'Pengembalian Ditolak',
    pesan: 'Pengajuan pengembalian alat untuk peminjaman '
        . $pengembalian->peminjaman->pinjam_code
        . ' ditolak oleh Petugas.',
    url: route('pengembalian.index'),
    jenis: 'pengembalian'
));

            return $pengembalian->fresh([
                'peminjaman.user',
                'details.detailPeminjaman.alat',
            ]);
        });
    }
}
