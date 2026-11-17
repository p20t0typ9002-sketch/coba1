<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Filament\Widgets\ChartWidget;

class TrenTransaksiChart extends ChartWidget
{
    protected ?string $heading = 'Tren Peminjaman dan Pengembalian';

    protected ?string $description = 'Jumlah transaksi setiap bulan berdasarkan tanggal transaksi.';

    protected ?string $maxHeight = '350px';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = null;

    public static function canView(): bool
    {
        return auth()->user()?->isPetugas() ?? false;
    }

    protected function getFilters(): ?array
    {
        $tahunSekarang = (int) now()->format('Y');

        return collect(range($tahunSekarang - 4, $tahunSekarang))
            ->mapWithKeys(fn ($tahun) => [
                (string) $tahun => (string) $tahun,
            ])
            ->all();
    }

    protected function getData(): array
    {
        $tahunSekarang = (int) now()->format('Y');

        $tahun = filter_var(
            $this->filter ?? (string) $tahunSekarang,
            FILTER_VALIDATE_INT
        );

        if (! $tahun || $tahun < $tahunSekarang - 4 || $tahun > $tahunSekarang) {
            $tahun = $tahunSekarang;
        }

        $peminjaman = Peminjaman::query()
            ->selectRaw('MONTH(tanggal_pinjam) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pinjam', $tahun)
            ->groupByRaw('MONTH(tanggal_pinjam)')
            ->pluck('total', 'bulan');

        $pengembalian = Pengembalian::query()
            ->selectRaw('MONTH(tanggal_kembali) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_kembali', $tahun)
            ->groupByRaw('MONTH(tanggal_kembali)')
            ->pluck('total', 'bulan');

        $labels = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        $dataPeminjaman = [];
        $dataPengembalian = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $dataPeminjaman[] = (int) ($peminjaman[$bulan] ?? 0);
            $dataPengembalian[] = (int) ($pengembalian[$bulan] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Peminjaman',
                    'data' => $dataPeminjaman,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Pengembalian',
                    'data' => $dataPengembalian,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
