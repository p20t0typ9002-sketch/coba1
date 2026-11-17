<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanExportController extends Controller
{
    private function validasiFilter(Request $request): array
    {
        abort_unless(
            auth()->user()?->isPetugas(),
            403,
            'Hanya Petugas yang dapat mengunduh laporan.'
        );

        return $request->validate([
            'jenis' => ['required', 'in:peminjaman,pengembalian'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);
    }

    private function ambilData(array $filter)
    {
        if ($filter['jenis'] === 'pengembalian') {
            return Pengembalian::with([
                'peminjaman.user',
                'petugasPenerima',
                'details.detailPeminjaman.alat',
            ])
                ->when($filter['dari'] ?? null, fn ($query, $tanggal) =>
                    $query->whereDate('tanggal_kembali', '>=', $tanggal)
                )
                ->when($filter['sampai'] ?? null, fn ($query, $tanggal) =>
                    $query->whereDate('tanggal_kembali', '<=', $tanggal)
                )
                ->latest('tanggal_kembali')
                ->get();
        }

        return Peminjaman::with([
            'user',
            'petugasPenyetuju',
            'details.alat',
        ])
            ->when($filter['dari'] ?? null, fn ($query, $tanggal) =>
                $query->whereDate('tanggal_pinjam', '>=', $tanggal)
            )
            ->when($filter['sampai'] ?? null, fn ($query, $tanggal) =>
                $query->whereDate('tanggal_pinjam', '<=', $tanggal)
            )
            ->latest('tanggal_pinjam')
            ->get();
    }

    private function formatTanggal($tanggal): string
    {
        return $tanggal
            ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y')
            : '-';
    }

    private function siapkanBaris($laporan, string $jenis): array
    {
        return $laporan->map(function ($item) use ($jenis) {
            if ($jenis === 'peminjaman') {
                $daftarAlat = $item->details
                    ->map(fn ($detail) =>
                        ($detail->alat?->name ?? 'Alat tidak tersedia')
                        . ' (' . $detail->jumlah . ')'
                    )
                    ->implode(', ');

                return [
                    $item->pinjam_code,
                    $item->user?->name ?? '-',
                    $this->formatTanggal($item->tanggal_pinjam),
                    $this->formatTanggal($item->tanggal_rencana_kembali),
                    $daftarAlat,
                    $item->petugasPenyetuju?->name ?? '-',
                    ucfirst($item->status),
                ];
            }

            $daftarAlat = $item->details
                ->map(fn ($detail) =>
                    ($detail->detailPeminjaman?->alat?->name ?? 'Alat tidak tersedia')
                    . ' (' . $detail->jumlah_kembali . ')'
                )
                ->implode(', ');

            return [
                $item->peminjaman?->pinjam_code ?? '-',
                $item->peminjaman?->user?->name ?? '-',
                $this->formatTanggal($item->tanggal_kembali),
                $daftarAlat,
                $item->petugasPenerima?->name ?? '-',
                ucfirst($item->status),
            ];
        })->all();
    }

    public function excel(Request $request)
    {
        $filter = $this->validasiFilter($request);
        $laporan = $this->ambilData($filter);

        if ($filter['jenis'] === 'peminjaman') {
            $headings = [
                'Kode Peminjaman',
                'Nama Peminjam',
                'Tanggal Pinjam',
                'Rencana Kembali',
                'Daftar Alat',
                'Petugas Penyetuju',
                'Status',
            ];
        } else {
            $headings = [
                'Kode Peminjaman',
                'Nama Peminjam',
                'Tanggal Kembali',
                'Daftar Alat',
                'Petugas Penerima',
                'Status',
            ];
        }

        $rows = $this->siapkanBaris($laporan, $filter['jenis']);

        $namaFile = 'laporan-' . $filter['jenis'] . '-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new LaporanExport($headings, $rows),
            $namaFile
        );
    }

    public function pdf(Request $request)
    {
        $filter = $this->validasiFilter($request);
        $laporan = $this->ambilData($filter);

        $data = [
            'jenis' => $filter['jenis'],
            'dari' => $filter['dari'] ?? null,
            'sampai' => $filter['sampai'] ?? null,
            'baris' => $this->siapkanBaris($laporan, $filter['jenis']),
        ];

        $pdf = Pdf::loadView('laporan.pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download(
            'laporan-' . $filter['jenis'] . '-' . now()->format('Ymd-His') . '.pdf'
        );
    }
}
