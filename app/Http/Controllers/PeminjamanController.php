<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Notifications\TransaksiNotification;

class PeminjamanController extends Controller
{
    /**
     * Memastikan halaman hanya dapat diakses oleh Peminjam.
     */
    private function pastikanPeminjam(): void
    {
        abort_unless(
            auth()->user()?->isPeminjam(),
            403,
            'Anda tidak memiliki akses ke halaman ini.'
        );
    }

    /**
     * Menampilkan katalog alat.
     */
    public function index()
    {
        $this->pastikanPeminjam();

        $alats = Alat::with('category', 'images')
            ->where('is_active', true)
            ->where('stok', '>', 0)
            ->latest()
            ->paginate(12);

        return view('peminjaman.index', compact('alats'));
    }

    /**
     * Menampilkan detail alat.
     */
    public function show(Alat $alat)
    {
        $this->pastikanPeminjam();

        abort_unless(
            $alat->is_active && $alat->stok > 0,
            404
        );

        $alat->load('category', 'images');

        return view('peminjaman.show', compact('alat'));
    }

    /**
     * Menampilkan formulir pengajuan.
     */
    public function create()
    {
        $this->pastikanPeminjam();

        $alats = Alat::with('category')
            ->where('is_active', true)
            ->where('stok', '>', 0)
            ->orderBy('name')
            ->get();

        return view('peminjaman.create', compact('alats'));
    }

    /**
     * Menyimpan pengajuan peminjaman.
     */
    public function store(Request $request)
    {
        $this->pastikanPeminjam();

        $validated = $request->validate([
            'tanggal_pinjam' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'tanggal_rencana_kembali' => [
                'required',
                'date',
                'after_or_equal:tanggal_pinjam',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'items' => [
                'required',
                'array',
            ],
            'items.*' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        // Ambil alat yang jumlahnya lebih dari nol.
        $itemsDipilih = collect($validated['items'])
            ->mapWithKeys(fn ($jumlah, $id) => [
                (int) $id => (int) $jumlah,
            ])
            ->filter(fn ($jumlah) => $jumlah > 0);

        if ($itemsDipilih->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Pilih minimal satu alat untuk dipinjam.',
            ]);
        }

        DB::transaction(function () use (
            $validated,
            $itemsDipilih
        ) {
            $user = auth()->user();

            // Kunci data alat untuk pemeriksaan stok.
            $alats = Alat::whereIn('id', $itemsDipilih->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Pastikan semua ID alat benar-benar tersedia.
            if ($alats->count() !== $itemsDipilih->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Ada alat yang tidak ditemukan.',
                ]);
            }

            // Periksa status aktif dan jumlah stok.
            foreach ($itemsDipilih as $alatId => $jumlah) {
                $alat = $alats->get($alatId);

                if (! $alat->is_active) {
                    throw ValidationException::withMessages([
                        'items' => "Alat {$alat->name} sedang tidak aktif.",
                    ]);
                }

                if ($jumlah > $alat->stok) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$alat->name} tidak mencukupi. Stok tersedia: {$alat->stok}.",
                    ]);
                }
            }

            // Buat kode peminjaman unik.
            do {
                $kode = 'PIN-' . now()->format('Ymd')
                    . '-' . Str::upper(Str::random(6));
            } while (Peminjaman::where('pinjam_code', $kode)->exists());

            // Simpan data utama peminjaman.
            $peminjaman = Peminjaman::create([
                'pinjam_code' => $kode,
                'user_id' => $user->id,
                'disetujui_oleh' => null,
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_rencana_kembali' => $validated['tanggal_rencana_kembali'],
                'status' => 'menunggu',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Simpan setiap alat yang dipilih.
            foreach ($itemsDipilih as $alatId => $jumlah) {
                DetailPeminjaman::create([
                    'pinjam_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlah,
                ]);
            }

            ActivityLogger::log(
                'pengajuan_peminjaman',
                "Mengajukan peminjaman dengan kode {$kode}.",
                $user
            );
        });

        // Kirim notifikasi kepada semua Petugas.
$petugas = User::where('level', 'petugas')->get();

foreach ($petugas as $userPetugas) {
    $userPetugas->notify(new TransaksiNotification(
        judul: 'Pengajuan Peminjaman Baru',
        pesan: 'Ada pengajuan peminjaman baru yang perlu diperiksa.',
        url: '/admin',
        jenis: 'peminjaman'
    ));
}

        return redirect()
            ->route('peminjaman.katalog')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }


    /**
        * Menampilkan riwayat peminjaman milik pengguna.
    */
    public function riwayat()
    {
        $this->pastikanPeminjam();

        $peminjamans = Peminjaman::with([
            'details.alat',
            'pengembalians.details',
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('peminjaman.riwayat', compact('peminjamans'));
    }

}
