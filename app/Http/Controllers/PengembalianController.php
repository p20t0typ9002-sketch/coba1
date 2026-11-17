<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Notifications\TransaksiNotification;

class PengembalianController extends Controller
{
    /**
     * Menampilkan riwayat peminjaman milik pengguna.
     */
    public function index()
    {
        abort_unless(auth()->user()->isPeminjam(), 403);

        $peminjamans = Peminjaman::query()
            ->where('user_id', auth()->id())
            ->with([
                'details.alat',
                'pengembalians.details',
            ])
            ->latest()
            ->get();

        return view('pengembalian.index', compact('peminjamans'));
    }

    /**
     * Menampilkan form pengajuan pengembalian.
     */
    public function create(Peminjaman $peminjaman)
    {
        abort_unless(auth()->user()->isPeminjam(), 403);
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        if ($peminjaman->status !== 'dipinjam') {
            return redirect()
                ->route('pengembalian.index')
                ->with('error', 'Peminjaman ini tidak sedang berstatus dipinjam.');
        }

        if ($peminjaman->pengembalians()->where('status', 'diajukan')->exists()) {
            return redirect()
                ->route('pengembalian.index')
                ->with('error', 'Masih ada pengajuan pengembalian yang menunggu petugas.');
        }

        $peminjaman->load('details.alat');

        $details = $peminjaman->details->map(function ($detail) {
            $sudahDikembalikan = $detail->detailPengembalians()
                ->whereHas('pengembalian', function ($query) {
                    $query->where('status', 'diterima');
                })
                ->sum('jumlah_kembali');

            $detail->sisa_pengembalian = max(
                0,
                $detail->jumlah - $sudahDikembalikan
            );

            return $detail;
        });

        return view('pengembalian.create', compact(
            'peminjaman',
            'details'
        ));
    }

    /**
     * Menyimpan pengajuan pengembalian.
     */
    public function store(Request $request, Peminjaman $peminjaman)
    {
        abort_unless(auth()->user()->isPeminjam(), 403);
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        $data = $request->validate([
            'tanggal_kembali' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],

            'details' => ['required', 'array'],
            'details.*.jumlah_kembali' => ['required', 'integer', 'min:0'],
            'details.*.kondisi_alat' => [
                'required',
                'in:baik,rusak_ringan,rusak_berat,hilang',
            ],
            'details.*.catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $adaJumlah = collect($data['details'])
            ->contains(fn ($detail) => (int) $detail['jumlah_kembali'] > 0);

        if (! $adaJumlah) {
            throw ValidationException::withMessages([
                'details' => 'Masukkan jumlah minimal satu alat yang dikembalikan.',
            ]);
        }

        DB::transaction(function () use ($data, $peminjaman) {
            $peminjaman = Peminjaman::query()
                ->whereKey($peminjaman->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($peminjaman->user_id !== auth()->id()) {
                abort(403);
            }

            if ($peminjaman->status !== 'dipinjam') {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Peminjaman ini tidak dapat dikembalikan.',
                ]);
            }

            if ($peminjaman->pengembalians()
                ->where('status', 'diajukan')
                ->exists()) {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Masih ada pengajuan yang menunggu petugas.',
                ]);
            }

            $pengembalian = $peminjaman->pengembalians()->create([
                'tanggal_kembali' => $data['tanggal_kembali'],
                'status' => 'diajukan',
                'catatan' => $data['catatan'] ?? null,
            ]);

            foreach ($data['details'] as $detailId => $detailData) {
                $jumlah = (int) $detailData['jumlah_kembali'];

                // Jumlah 0 berarti alat tersebut tidak ikut dikembalikan.
                if ($jumlah === 0) {
                    continue;
                }

                $detailPinjam = $peminjaman->details()
                    ->whereKey($detailId)
                    ->lockForUpdate()
                    ->first();

                if (! $detailPinjam) {
                    throw ValidationException::withMessages([
                        'details' => 'Detail alat tidak sesuai dengan peminjaman.',
                    ]);
                }

                $sudahDikembalikan = $detailPinjam
                    ->detailPengembalians()
                    ->whereHas('pengembalian', function ($query) {
                        $query->where('status', 'diterima');
                    })
                    ->sum('jumlah_kembali');

                $sisa = $detailPinjam->jumlah - $sudahDikembalikan;

                if ($jumlah > $sisa) {
                    throw ValidationException::withMessages([
                        'details' => "Jumlah pengembalian melebihi sisa alat yang dipinjam.",
                    ]);
                }

                $pengembalian->details()->create([
                    'detail_pinjam_id' => $detailPinjam->id,
                    'jumlah_kembali' => $jumlah,
                    'kondisi_alat' => $detailData['kondisi_alat'],
                    'denda' => 0,
                    'catatan' => $detailData['catatan'] ?? null,
                ]);
            }
        });

        $petugas = User::where('level', 'petugas')->get();

foreach ($petugas as $userPetugas) {
    $userPetugas->notify(new TransaksiNotification(
        judul: 'Pengajuan Pengembalian Baru',
        pesan: 'Ada pengajuan pengembalian alat yang perlu diperiksa.',
        url: '/admin',
        jenis: 'pengembalian'
    ));
}

        return redirect()
            ->route('pengembalian.index')
            ->with('success', 'Pengajuan pengembalian berhasil dikirim dan menunggu pemeriksaan petugas.');
    }
}
