
@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    <div class="dashboard-heading">
        <div>
            <span class="dashboard-eyebrow">DASHBOARD PEMINJAM</span>
            <h1>Selamat datang, {{ auth()->user()->name }}!</h1>
            <p>Pantau aktivitas peminjaman dan pengembalian alatmu di sini.</p>
        </div>

        <a href="{{ route('peminjaman.katalog') }}" class="btn-primary">
            <span>+</span> Pinjam Alat
        </a>
    </div>

    <div class="dashboard-stats">

        <div class="stat-card">
            <div class="stat-icon blue">▦</div>
            <div>
                <p>Alat Tersedia</p>
                <h2>{{ $ringkasan['alat_tersedia'] }}</h2>
                <span>Jenis alat dengan stok tersedia</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">◷</div>
            <div>
                <p>Menunggu Persetujuan</p>
                <h2>{{ $ringkasan['menunggu'] }}</h2>
                <span>Pengajuan peminjaman</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">▣</div>
            <div>
                <p>Peminjaman Aktif</p>
                <h2>{{ $ringkasan['dipinjam'] }}</h2>
                <span>Transaksi sedang berlangsung</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple">✓</div>
            <div>
                <p>Transaksi Selesai</p>
                <h2>{{ $ringkasan['selesai'] }}</h2>
                <span>Peminjaman telah diselesaikan</span>
            </div>
        </div>

    </div>

    <div class="dashboard-content">

        <section class="dashboard-panel">
            <div class="panel-heading">
                <div>
                    <h2>Peminjaman Terbaru</h2>
                    <p>Lima transaksi peminjaman terakhirmu.</p>
                </div>

                <a href="{{ route('peminjaman.riwayat') }}">
                    Lihat Semua →
                </a>
            </div>

            @forelse ($peminjamanTerbaru as $peminjaman)
                <div class="loan-item">
                    <div class="loan-info">
                        <strong>{{ $peminjaman->pinjam_code }}</strong>

                        <span>
                            {{ $peminjaman->details->count() }}
                            jenis alat
                            ·
                            {{ $peminjaman->tanggal_pinjam
                                ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y')
                                : '-' }}
                        </span>

                        <div class="loan-tools">
                            @foreach ($peminjaman->details as $detail)
                                <span>
                                    {{ $detail->alat?->name ?? 'Alat tidak tersedia' }}
                                    ({{ $detail->jumlah }})
                                </span>
                            @endforeach
                        </div>
                    </div>

                    @php
                        $statusClass = match ($peminjaman->status) {
                            'menunggu' => 'status-waiting',
                            'dipinjam' => 'status-active',
                            'ditolak' => 'status-rejected',
                            'selesai' => 'status-completed',
                            default => 'status-default',
                        };

                        $statusLabel = match ($peminjaman->status) {
                            'menunggu' => 'Menunggu',
                            'dipinjam' => 'Sedang Dipinjam',
                            'ditolak' => 'Ditolak',
                            'selesai' => 'Selesai',
                            default => ucfirst($peminjaman->status),
                        };
                    @endphp

                    <span class="status-badge {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">▤</div>
                    <h3>Belum Ada Peminjaman</h3>
                    <p>Kamu belum memiliki transaksi peminjaman alat.</p>
                    <a href="{{ route('peminjaman.katalog') }}" class="btn-primary">
                        Jelajahi Katalog Alat
                    </a>
                </div>
            @endforelse
        </section>

        <aside class="dashboard-panel quick-panel">
            <div class="panel-heading">
                <div>
                    <h2>Akses Cepat</h2>
                    <p>Fitur yang sering digunakan.</p>
                </div>
            </div>

            <a href="{{ route('peminjaman.katalog') }}" class="quick-link">
                <span class="quick-icon blue">⌕</span>
                <span>
                    <strong>Katalog Alat</strong>
                    <small>Cari alat yang ingin dipinjam</small>
                </span>
                <span class="quick-arrow">→</span>
            </a>

            <a href="{{ route('peminjaman.riwayat') }}" class="quick-link">
                <span class="quick-icon green">▤</span>
                <span>
                    <strong>Riwayat Peminjaman</strong>
                    <small>Lihat status transaksi</small>
                </span>
                <span class="quick-arrow">→</span>
            </a>

            <a href="{{ route('pengembalian.index') }}" class="quick-link">
                <span class="quick-icon orange">↩</span>
                <span>
                    <strong>Pengembalian Alat</strong>
                    <small>Ajukan pengembalian alat</small>
                </span>
                <span class="quick-arrow">→</span>
            </a>
        </aside>

    </div>
</div>

<style>
.dashboard-container {
    max-width: 1440px;
    margin: 0 auto;
    padding: 34px 0 60px;
    color: #172033;
}

.dashboard-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.dashboard-eyebrow {
    color: #2563eb;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1.5px;
}

.dashboard-heading h1 {
    margin: 8px 0;
    font-size: clamp(25px, 3vw, 34px);
    font-weight: 800;
    letter-spacing: -1px;
}

.dashboard-heading p,
.panel-heading p {
    color: #64748b;
    margin: 0;
    font-size: 14px;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 18px;
    background: #2563eb;
    color: white !important;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    white-space: nowrap;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 26px;
}

.stat-card {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    background: white;
    border: 1px solid #e8edf4;
    border-radius: 15px;
    padding: 22px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, .035);
}

.stat-icon,
.quick-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 45px;
    height: 45px;
    border-radius: 12px;
    font-size: 23px;
    font-weight: 700;
}

.blue { background: #eff6ff; color: #2563eb; }
.orange { background: #fff7ed; color: #ea580c; }
.green { background: #ecfdf5; color: #059669; }
.purple { background: #f5f3ff; color: #7c3aed; }

.stat-card p {
    margin: 2px 0 7px;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
}

.stat-card h2 {
    margin: 0 0 5px;
    font-size: 29px;
    font-weight: 800;
}

.stat-card span {
    color: #94a3b8;
    font-size: 11px;
}

.dashboard-content {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(280px, 1fr);
    gap: 22px;
}

.dashboard-panel {
    background: white;
    border: 1px solid #e8edf4;
    border-radius: 15px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, .035);
    min-width: 0;
}

.panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.panel-heading h2 {
    margin: 0 0 6px;
    font-size: 18px;
    font-weight: 800;
}

.panel-heading > a {
    color: #2563eb;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
}

.loan-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 19px 0;
    border-top: 1px solid #eef2f7;
}

.loan-info {
    min-width: 0;
}

.loan-info strong {
    display: block;
    font-size: 14px;
    margin-bottom: 6px;
}

.loan-info > span {
    color: #64748b;
    font-size: 12px;
}

.loan-tools {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}

.loan-tools span {
    background: #f1f5f9;
    color: #475569;
    border-radius: 6px;
    padding: 5px 8px;
    font-size: 11px;
}

.status-badge {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.status-waiting { background: #fff7ed; color: #c2410c; }
.status-active { background: #eff6ff; color: #1d4ed8; }
.status-rejected { background: #fef2f2; color: #dc2626; }
.status-completed { background: #ecfdf5; color: #047857; }
.status-default { background: #f1f5f9; color: #475569; }

.quick-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 0;
    border-top: 1px solid #eef2f7;
    text-decoration: none;
    color: inherit;
}

.quick-link > span:nth-child(2) {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 0;
}

.quick-link strong {
    font-size: 13px;
}

.quick-link small {
    color: #94a3b8;
    font-size: 11px;
}

.quick-icon {
    width: 40px;
    height: 40px;
    font-size: 20px;
}

.quick-arrow {
    margin-left: auto;
    color: #94a3b8;
}

.quick-link:hover strong,
.quick-link:hover .quick-arrow {
    color: #2563eb;
}

.empty-state {
    text-align: center;
    padding: 38px 15px;
    border-top: 1px solid #eef2f7;
}

.empty-icon {
    font-size: 36px;
    color: #94a3b8;
}

.empty-state h3 {
    margin: 12px 0 6px;
    font-size: 16px;
}

.empty-state p {
    margin: 0 0 18px;
    color: #64748b;
    font-size: 13px;
}

@media (max-width: 1050px) {
    .dashboard-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 600px) {
    .dashboard-container {
        padding: 24px 0 40px;
    }

    .dashboard-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .dashboard-stats {
        gap: 10px;
    }

    .stat-card {
        padding: 15px;
        gap: 10px;
    }

    .stat-icon {
        width: 36px;
        height: 36px;
        font-size: 19px;
    }

    .stat-card h2 {
        font-size: 24px;
    }

    .stat-card span {
        line-height: 1.5;
    }

    .dashboard-panel {
        padding: 18px;
    }

    .loan-item {
        flex-direction: column;
    }
}
</style>
@endsection
