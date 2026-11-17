<?php

namespace App\Filament\Pages;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Filament\Pages\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;
use App\Filament\Widgets\TrenTransaksiChart;
use App\Filament\Widgets\KondisiAlatChart;

class Laporan extends Page
{
    use WithPagination;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Transaksi';

    protected static ?string $title = 'Laporan Transaksi';

    protected static string|\UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.laporan';

    public string $jenis = 'peminjaman';

    public ?string $dari = null;

    public ?string $sampai = null;

    /**
     * Hanya Petugas yang boleh mengakses halaman laporan.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->isPetugas() ?? false;
    }

    /**
     * Mengatur jenis laporan.
     */
    public function pilihJenis(string $jenis): void
    {
        if (! in_array($jenis, ['peminjaman', 'pengembalian'], true)) {
            return;
        }

        $this->jenis = $jenis;
        $this->resetPage();
    }

    /**
     * Reset pagination ketika filter tanggal berubah.
     */
    public function updatedDari(): void
    {
        $this->resetPage();
    }

    public function updatedSampai(): void
    {
        $this->resetPage();
    }

    /**
     * Mengambil data laporan sesuai filter.
     */
    public function getLaporan(): LengthAwarePaginator
    {
        if ($this->jenis === 'pengembalian') {
            return Pengembalian::with([
                'peminjaman.user',
                'petugasPenerima',
                'details.detailPeminjaman.alat',
            ])
                ->when($this->dari, fn ($query) =>
                    $query->whereDate('tanggal_kembali', '>=', $this->dari)
                )
                ->when($this->sampai, fn ($query) =>
                    $query->whereDate('tanggal_kembali', '<=', $this->sampai)
                )
                ->latest('tanggal_kembali')
                ->paginate(15);
        }

        return Peminjaman::with([
            'user',
            'petugasPenyetuju',
            'details.alat',
        ])
            ->when($this->dari, fn ($query) =>
                $query->whereDate('tanggal_pinjam', '>=', $this->dari)
            )
            ->when($this->sampai, fn ($query) =>
                $query->whereDate('tanggal_pinjam', '<=', $this->sampai)
            )
            ->latest('tanggal_pinjam')
            ->paginate(15);
    }

    /**
     * Menghapus filter.
     */
    public function resetFilter(): void
    {
        $this->reset(['dari', 'sampai']);
        $this->resetPage();
    }


public function getRingkasan(): array
{
    if ($this->jenis === 'pengembalian') {
        $query = Pengembalian::query()
            ->when($this->dari, fn ($query) =>
                $query->whereDate('tanggal_kembali', '>=', $this->dari)
            )
            ->when($this->sampai, fn ($query) =>
                $query->whereDate('tanggal_kembali', '<=', $this->sampai)
            );

        return [
            'total' => (clone $query)->count(),
            'diajukan' => (clone $query)->where('status', 'diajukan')->count(),
            'diterima' => (clone $query)->where('status', 'diterima')->count(),
            'ditolak' => (clone $query)->where('status', 'ditolak')->count(),
        ];
    }

    $query = Peminjaman::query()
        ->when($this->dari, fn ($query) =>
            $query->whereDate('tanggal_pinjam', '>=', $this->dari)
        )
        ->when($this->sampai, fn ($query) =>
            $query->whereDate('tanggal_pinjam', '<=', $this->sampai)
        );

    return [
        'total' => (clone $query)->count(),
        'menunggu' => (clone $query)->where('status', 'menunggu')->count(),
        'dipinjam' => (clone $query)->where('status', 'dipinjam')->count(),
        'ditolak' => (clone $query)->where('status', 'ditolak')->count(),
        'selesai' => (clone $query)->where('status', 'selesai')->count(),
    ];
}

    protected function getHeaderWidgets(): array
    {
        return [
            TrenTransaksiChart::class,
            KondisiAlatChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

}
