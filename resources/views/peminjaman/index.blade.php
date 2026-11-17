
@extends('layouts.app')

@section('title', 'Katalog Alat - Sistem Peminjaman')

@push('styles')
<style>
    .catalog-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .tool-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        transition: .2s;
    }

    .tool-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, .08);
    }

    .tool-image {
        width: 100%;
        height: 210px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #2563eb;
        font-size: 45px;
    }

    .tool-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tool-content {
        padding: 20px;
    }

    .tool-category {
        display: inline-block;
        color: #1d4ed8;
        background: #dbeafe;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .tool-content h3 {
        font-size: 18px;
        color: #0f172a;
        margin-bottom: 9px;
    }

    .tool-description {
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
        min-height: 44px;
        margin-bottom: 15px;
    }

    .tool-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 13px 0;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        font-size: 12px;
        color: #64748b;
        margin-bottom: 15px;
    }

    .tool-meta strong {
        color: #334155;
    }

    .empty-state {
        background: white;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 55px 20px;
        text-align: center;
        color: #64748b;
    }

    .pagination {
        margin-top: 25px;
    }

    @media(max-width: 900px) {
        .catalog-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 580px) {
        .catalog-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="catalog-header">
        <div class="page-heading" style="margin-bottom: 0;">
            <h1>Katalog Alat</h1>
            <p>
                Temukan peralatan yang tersedia dan ajukan peminjaman
                sesuai kebutuhanmu.
            </p>
        </div>

        <a href="{{ route('peminjaman.create') }}" class="btn">
            + Buat Pengajuan
        </a>
    </div>

    @if($alats->count())
        <div class="catalog-grid">
            @foreach($alats as $alat)
                @php
                    $gambar = $alat->images->firstWhere('is_primary', true)
                        ?? $alat->images->first();
                @endphp

                <article class="tool-card">
                    <div class="tool-image">
                        @if($gambar)
                            <img
                                src="{{ asset('storage/' . $gambar->image_path) }}"
                                alt="{{ $gambar->alt_text ?: $alat->name }}"
                            >
                        @else
                            📦
                        @endif
                    </div>

                    <div class="tool-content">
                        <span class="tool-category">
                            {{ $alat->category?->name ?? 'Tanpa kategori' }}
                        </span>

                        <h3>{{ $alat->name }}</h3>

                        <p class="tool-description">
                            {{ \Illuminate\Support\Str::limit($alat->description ?? 'Tidak ada deskripsi.', 100) }}
                        </p>

                        <div class="tool-meta">
                            <span>
                                Stok: <strong>{{ $alat->stok }}</strong>
                            </span>

                            <span>
                                Kondisi: <strong>{{ ucfirst($alat->kondisi) }}</strong>
                            </span>
                        </div>

                        <a
                            href="{{ route('peminjaman.show', $alat->slug) }}"
                            class="btn"
                            style="width: 100%; text-align: center;"
                        >
                            Lihat Detail
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            {{ $alats->links() }}
        </div>
    @else
        <div class="empty-state">
            <div style="font-size: 45px; margin-bottom: 15px;">📦</div>
            <h3 style="color: #334155; margin-bottom: 8px;">
                Belum Ada Alat Tersedia
            </h3>
            <p>
                Saat ini belum ada peralatan aktif dengan stok tersedia.
            </p>
        </div>
    @endif
@endsection
