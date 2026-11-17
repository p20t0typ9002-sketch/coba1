
@extends('layouts.app')

@section('title', 'Laporan Sistem Peminjaman Alat')

@push('styles')
<style>
    .report-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .filter-card {
        margin-bottom: 25px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        align-items: end;
        gap: 15px;
    }

    .filter-group label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 11px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        background: white;
    }

    .report-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .report-tab {
        display: inline-block;
        padding: 11px 17px;
        border-radius: 8px;
        background: #e2e8f0;
        color: #334155;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
    }

    .report-tab.active {
        background: #2563eb;
        color: white;
    }

    .report-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
    }

    .report-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .report-card-header h2 {
        font-size: 18px;
        color: #0f172a;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 750px;
    }

    .report-table th,
    .report-table td {
        padding: 13px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: top;
    }

    .report-table th {
        color: #64748b;
        background: #f8fafc;
        font-size: 12px;
    }

    .report-table td {
        color: #334155;
    }

    .status {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-menunggu,
    .status-diajukan {
        color: #92400e;
        background: #fef3c7;
    }

    .status-ditolak {
        color: #991b1b;
        background: #fee2e2;
    }

    .status-dipinjam {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .status-selesai,
    .status-diterima {
        color: #166534;
        background: #dcfce7;
    }

    .empty-report {
        padding: 45px 20px;
        text-align: center;
        color: #64748b;
    }

    .report-pagination {
        margin-top: 20px;
    }

    @media(max-width: 850px) {
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width: 500px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .report-card {
            padding: 15px;
        }
    }

    @media print {
        body {
            background: white !important;
        }

        header,
        footer,
        .no-print,
        .report-pagination {
            display: none !important;
        }

        main {
            padding: 0 !important;
        }

        .container {
            max-width: 100% !important;
        }

        .report-card {
            border: none;
            box-shadow: none;
            padding: 0;
        }

        .table-wrapper {
            overflow: visible;
        }

        .report-table {
            min-width: 0;
            font-size: 10px;
        }

        .report-table th,
        .report-table td {
            padding: 7px;
        }

        .report-table tr {
            break-inside: avoid;
        }

        .status {
            border: 1px solid #999;
            background: white !important;
            color: black !important;
        }

        @page {
            size: landscape;
            margin: 12mm;
        }
    }
</style>
@endpush

@section('content')

    <div class="report-heading">
        <div class="page-heading" style="margin-bottom: 0;">
            <h1>Laporan Transaksi</h1>
            <p>
                Laporan aktivitas peminjaman dan pengembalian alat.
            </p>
        </div>

        <button
            type="button"
            class="btn no-print"
            onclick="window.print()"
        >
            Cetak Laporan
        </button>
    </div>

    <div class="report-tabs no-print">
        <a
            href="{{ route('laporan.index', array_merge(request()->query(), ['jenis' => 'peminjaman'])) }}"
            class="report-tab {{ $jenis === 'peminjaman' ? 'active' : '' }}"
        >
            Laporan Peminjaman
        </a>

        <a
            href="{{ route('laporan.index', array_merge(request()->query(), ['jenis' => 'pengembalian'])) }}"
            class="report-tab {{ $jenis === 'pengembalian' ? 'active' : '' }}"
        >
            Laporan Pengembalian
        </a>
    </div>

    <div class="card filter-card no-print">
        <form method="GET" action="{{ route('laporan.index') }}">
            <input type="hidden" name="jenis" value="{{ $jenis }}">

            <div class="filter-grid">
                <div class="filter-group">
                    <label for="dari">Tanggal Dari</label>
                    <input
                        type="date"
                        id="dari"
                        name="dari"
                        value="{{ $dari }}"
                    >
                </div>

                <div class="filter-group">
                    <label for="sampai">Tanggal Sampai</label>
                    <input
                        type="date"
                        id="sampai"
                        name="sampai"
                        min="{{ $dari }}"
                        value="{{ $sampai }}"
                    >
                </div>

                <div class="filter-group">
                    <label>Jenis Laporan</label>
                    <select
                        onchange="window.location.href='{{ route('laporan.index') }}?jenis='+this.value"
                    >
                        <option value="peminjaman"
                            {{ $jenis === 'peminjaman' ? 'selected' : '' }}>
                            Peminjaman
                        </option>

                        <option value="pengembalian"
                            {{ $jenis === 'pengembalian' ? 'selected' : '' }}>
                            Pengembalian
                        </option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn">
                        Filter
                    </button>

                    <a href="{{ route('laporan.index', ['jenis' => $jenis]) }}"
                       class="btn btn-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="report-card">
        <div class="report-card-header">
            <h2>
                {{ $jenis === 'peminjaman'
                    ? 'Data Peminjaman'
                    : 'Data Pengembalian' }}
            </h2>

            <span style="font-size: 12px; color: #64748b;">
                Total pada halaman: {{ $laporan->count() }} transaksi
            </span>
        </div>

        <div class="table-wrapper">
            @if($laporan->isEmpty())
                <div class="empty-report">
                    <div style="font-size: 40px; margin-bottom: 12px;">
                        📋
                    </div>

                    <strong>Belum Ada Data Laporan</strong>

                    <p style="margin-top: 8px; font-size: 13px;">
                        Tidak ditemukan transaksi pada rentang tanggal ini.
                    </p>
                </div>
            @else
                <table class="report-table">
                    @if($jenis === 'peminjaman')
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Peminjaman</th>
                                <th>Peminjam</th>
                                <th>Tanggal Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Daftar Alat</th>
                                <th>Disetujui Oleh</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($laporan as $index => $item)
                                <tr>
                                    <td>
                                        {{ $laporan->firstItem() + $index }}
                                    </td>

                                    <td>
                                        <strong>{{ $item->pinjam_code }}</strong>
                                    </td>

                                    <td>
                                        {{ $item->user?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->tanggal_pinjam->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        {{ $item->tanggal_rencana_kembali->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        @foreach($item->details as $detail)
                                            <div>
                                                {{ $detail->alat?->name ?? 'Alat tidak tersedia' }}
                                                ({{ $detail->jumlah }})
                                            </div>
                                        @endforeach
                                    </td>

                                    <td>
                                        {{ $item->petugasPenyetuju?->name ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="status status-{{ $item->status }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @else
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Peminjaman</th>
                                <th>Peminjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Daftar Alat</th>
                                <th>Diterima Oleh</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($laporan as $index => $item)
                                <tr>
                                    <td>
                                        {{ $laporan->firstItem() + $index }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $item->peminjaman?->pinjam_code ?? '-' }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $item->peminjaman?->user?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->tanggal_kembali->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        @foreach($item->details as $detail)
                                            <div>
                                                {{ $detail->detailPeminjaman?->alat?->name ?? 'Alat tidak tersedia' }}
                                                ({{ $detail->jumlah_kembali }})
                                            </div>
                                        @endforeach
                                    </td>

                                    <td>
                                        {{ $item->petugasPenerima?->name ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="status status-{{ $item->status }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                </table>
            @endif
        </div>

        <div class="report-pagination no-print">
            {{ $laporan->links() }}
        </div>
    </div>

@endsection
