<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class KondisiAlatChart extends ChartWidget
{
    protected ?string $heading = 'Rekap Kondisi Alat';

    protected ?string $description =
        'Jumlah unit berdasarkan kondisi pada pengembalian yang telah diterima.';

    protected ?string $maxHeight = '350px';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isPetugas() ?? false;
    }

    protected function getData(): array
    {
        $hasil = DB::table('detail_pengembalians')
            ->join(
                'pengembalians',
                'detail_pengembalians.pengembalian_id',
                '=',
                'pengembalians.id'
            )
            ->where('pengembalians.status', 'diterima')
            ->select(
                'detail_pengembalians.kondisi_alat',
                DB::raw('SUM(detail_pengembalians.jumlah_kembali) as total')
            )
            ->groupBy('detail_pengembalians.kondisi_alat')
            ->pluck('total', 'kondisi_alat');

        $kategori = [
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
        ];

        $data = [];
        $labels = [];

        foreach ($kategori as $key => $label) {
            $labels[] = $label;
            $data[] = (int) ($hasil[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Unit',
                    'data' => $data,
                    'backgroundColor' => [
                        '#16A34A',
                        '#EAB308',
                        '#F97316',
                        '#DC2626',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
