
@extends('layouts.app')

@section('title', $alat->name . ' - Detail Alat')

@push('styles')
<style>
    .detail-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 35px;
        align-items: start;
    }

    .detail-image {
        width: 100%;
        height: 380px;
        background: #eff6ff;
        border-radius: 15px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 65px;
    }

    .detail-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .detail-info h1 {
        font-size: 30px;
        color: #0f172a;
        margin: 15px 0;
    }

    .detail-description {
        color: #64748b;
        line-height: 1.8;
        font-size: 14px;
        margin-bottom: 25px;
        white-space: pre-line;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
    }

    .detail-row span {
        color: #64748b;
    }

    .detail-row strong {
        color: #334155;
        text-align: right;
    }

    .detail-actions {
        display: flex;
        gap: 12px;
        margin-top: 25px;
        flex-wrap: wrap;
    }

    @media(max-width: 768px) {
        .detail-layout {
            grid-template-columns: 1fr;
        }

        .detail-image {
            height: 260px;
        }

        .detail-info h1 {
            font-size: 25px;
        }
    }
</style>
@endpush

@section('content')
    @php
        $gambar = $alat->images->firstWhere('is_primary', true)
            ?? $alat->images->first();
    @endphp

    <div class="page-heading">
        <h1>Detail Peralatan</h1>
        <p>Informasi lengkap mengenai peralatan yang tersedia.</p>
    </div>

    <div class="card">
        <div class="detail-layout">
            <div class="detail-image">
                @if($gambar)
                    <img
                        src="{{ asset('storage/' . $gambar->image_path) }}"
                        alt="{{ $gambar->alt_text ?: $alat->name }}"
                    >
                @else
                    📦
                @endif
            </div>

            <div class="detail-info">
                <span class="tool-category">
                    {{ $alat->category?->name ?? 'Tanpa kategori' }}
                </span>

                <h1>{{ $alat->name }}</h1>

                <p class="detail-description">
                    {{ $alat->description ?: 'Tidak ada deskripsi untuk alat ini.' }}
                </p>

                <div class="detail-row">
                    <span>Stok tersedia</span>
                    <strong>{{ $alat->stok }} unit</strong>
                </div>

                <div class="detail-row">
                    <span>Kondisi</span>
                    <strong>{{ ucfirst($alat->kondisi) }}</strong>
                </div>

                <div class="detail-row">
                    <span>Lokasi</span>
                    <strong>{{ $alat->location ?: '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Kategori</span>
                    <strong>{{ $alat->category?->name ?? '-' }}</strong>
                </div>

                <div class="detail-actions">
                    <a href="{{ route('peminjaman.create') }}" class="btn">
                        Ajukan Peminjaman
                    </a>

                    <a
                        href="{{ route('peminjaman.katalog') }}"
                        class="btn btn-secondary"
                    >
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
