<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Pastikan hanya Peminjam yang dapat membuka dashboard ini.
        abort_unless($user->isPeminjam(), 403);

        $peminjamanUser = Peminjaman::where('user_id', $user->id);

        $ringkasan = [
            'alat_tersedia' => Alat::where('is_active', true)
                ->where('stok', '>', 0)
                ->count(),

            'menunggu' => (clone $peminjamanUser)
                ->where('status', 'menunggu')
                ->count(),

            'dipinjam' => (clone $peminjamanUser)
                ->where('status', 'dipinjam')
                ->count(),

            'selesai' => (clone $peminjamanUser)
                ->where('status', 'selesai')
                ->count(),
        ];

        $peminjamanTerbaru = (clone $peminjamanUser)
            ->with(['details.alat'])
            ->latest()
            ->take(5)
            ->get();

        return view('peminjam.dashboard', compact(
            'ringkasan',
            'peminjamanTerbaru'
        ));
    }
}
