<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Memastikan hanya Petugas yang dapat melihat laporan.
     */
    private function pastikanPetugas(): void
    {
        abort_unless(
            auth()->user()?->isPetugas(),
            403,
            'Hanya Petugas yang dapat mengakses laporan.'
        );
    }

    /**
     * Menampilkan laporan peminjaman dan pengembalian.
     */
    public function index(Request $request)
    {
        $this->pastikanPetugas();

        $validated = $request->validate([
            'jenis' => ['nullable', 'in:peminjaman,pengembalian'],
            'dari' => ['nullable', 'date'],
            'sampai' => [
                'nullable',
                'date',
                'after_or_equal:dari',
            ],
        ]);

        $jenis = $validated['jenis'] ?? 'peminjaman';
        $dari = $validated['dari'] ?? null;
        $sampai = $validated['sampai'] ?? null;

        if ($jenis === 'peminjaman') {
            $laporan = Peminjaman::with([
                'user',
                'petugasPenyetuju',
                'details.alat',
            ])
                ->when($dari, fn ($query) =>
                    $query->whereDate('tanggal_pinjam', '>=', $dari)
                )
                ->when($sampai, fn ($query) =>
                    $query->whereDate('tanggal_pinjam', '<=', $sampai)
                )
                ->latest('tanggal_pinjam')
                ->paginate(15)
                ->withQueryString();
        } else {
            $laporan = Pengembalian::with([
                'peminjaman.user',
                'petugasPenerima',
                'details.detailPeminjaman.alat',
            ])
                ->when($dari, fn ($query) =>
                    $query->whereDate('tanggal_kembali', '>=', $dari)
                )
                ->when($sampai, fn ($query) =>
                    $query->whereDate('tanggal_kembali', '<=', $sampai)
                )
                ->latest('tanggal_kembali')
                ->paginate(15)
                ->withQueryString();
        }

        return view('laporan.index', compact(
            'laporan',
            'jenis',
            'dari',
            'sampai'
        ));
    }
}
