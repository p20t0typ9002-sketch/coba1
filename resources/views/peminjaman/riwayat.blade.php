
@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@push('styles')
<style>
    .loan-list {
        display: grid;
        gap: 20px;
    }

    .loan-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
    }

    .loan-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        flex-wrap: wrap;
        padding-bottom: 17px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 17px;
    }

    .loan-code {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .loan-date {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
    }

    .status {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-menunggu {
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

    .status-selesai {
        color: #166534;
        background: #dcfce7;
    }

    .loan-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-item span {
        display: block;
        font-size: 12px;
        color: #64748b;
        margin-bottom: 5px;
    }

    .info-item strong {
        font-size: 14px;
        color: #334155;
    }

    .loan-card h3 {
        font-size: 14px;
        color: #334155;
        margin-bottom: 12px;
    }

    .tool-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .tool-table th,
    .tool-table td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .tool-table th {
        color: #64748b;
        background: #f8fafc;
    }

    .loan-actions {
        margin-top: 18px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .empty-state {
        background: white;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 50px 20px;
        text-align: center;
        color: #64748b;
    }

    @media(max-width: 600px) {
        .loan-info {
            grid-template-columns: 1fr;
        }

        .loan-card {
            padding: 17px;
        }

        .table-wrapper {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')
    <div class="page-heading">
        <h1>Riwayat Peminjaman</h1>
        <p>
            Lihat seluruh pengajuan peminjaman dan status
            peralatan yang pernah kamu ajukan.
        </p>
    </div>

    <div style="margin-bottom: 22px;">
        <a href="{{ route('peminjaman.katalog') }}" class="btn">
            + Ajukan Peminjaman Baru
        </a>
    </div>

    @if($peminjamans->isEmpty())
        <div class="empty-state">
            <div style="font-size: 45px; margin-bottom: 15px;">📋</div>

            <h3 style="color: #334155; margin-bottom: 8px;">
                Belum Ada Riwayat Peminjaman
            </h3>

            <p style="margin-bottom: 18px;">
                Kamu belum pernah mengajukan peminjaman alat.
            </p>

            <a href="{{ route('peminjaman.katalog') }}" class="btn">
                Lihat Katalog Alat
            </a>
        </div>
    @else
        <div class="loan-list">
            @foreach($peminjamans as $peminjaman)
                <article class="loan-card">
                    <div class="loan-header">
                        <div>
                            <div class="loan-code">
                                {{ $peminjaman->pinjam_code }}
                            </div>

                            <div class="loan-date">
                                Diajukan:
                                {{ $peminjaman->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>

                        <span class="status status-{{ $peminjaman->status }}">
                            {{ ucfirst($peminjaman->status) }}
                        </span>
                    </div>

                    <div class="loan-info">
                        <div class="info-item">
                            <span>Tanggal Pinjam</span>
                            <strong>
                                {{ $peminjaman->tanggal_pinjam->format('d M Y') }}
                            </strong>
                        </div>

                        <div class="info-item">
                            <span>Rencana Kembali</span>
                            <strong>
                                {{ $peminjaman->tanggal_rencana_kembali->format('d M Y') }}
                            </strong>
                        </div>

                        <div class="info-item">
                            <span>Disetujui Oleh</span>
                            <strong>
                                {{ $peminjaman->petugasPenyetuju?->name ?? '-' }}
                            </strong>
                        </div>

                        <div class="info-item">
                            <span>Jumlah Jenis Alat</span>
                            <strong>
                                {{ $peminjaman->details->count() }} jenis
                            </strong>
                        </div>
                    </div>

                    <h3>Daftar Peralatan</h3>

                    <div class="table-wrapper">
                        <table class="tool-table">
                            <thead>
                                <tr>
                                    <th>Nama Alat</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($peminjaman->details as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->alat?->name ?? 'Alat tidak tersedia' }}
                                        </td>
                                        <td>{{ $detail->jumlah }} unit</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($peminjaman->notes)
                        <div style="margin-top: 16px; font-size: 13px;">
                            <strong>Catatan:</strong>
                            {{ $peminjaman->notes }}
                        </div>
                    @endif

                    <div class="loan-actions">
                        @if($peminjaman->status === 'dipinjam')
                            <a
                                href="{{ route('pengembalian.create', $peminjaman) }}"
                                class="btn"
                            >
                                Ajukan Pengembalian
                            </a>
                        @endif

                        @if($peminjaman->status === 'menunggu')
                            <span style="font-size: 13px; color: #92400e;">
                                Pengajuan sedang menunggu persetujuan Petugas.
                            </span>
                        @elseif($peminjaman->status === 'ditolak')
                            <span style="font-size: 13px; color: #991b1b;">
                                Pengajuan ini ditolak.
                            </span>
                        @elseif($peminjaman->status === 'selesai')
                            <span style="font-size: 13px; color: #166534;">
                                Peminjaman telah selesai diproses.
                            </span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div style="margin-top: 25px;">
            {{ $peminjamans->links() }}
        </div>
    @endif
@endsection
