
@extends('layouts.app')

@section('title', 'Pengajuan Peminjaman')

@push('styles')
<style>
    .form-card {
        max-width: 900px;
        margin: 0 auto;
    }

    .form-section-title {
        font-size: 17px;
        color: #0f172a;
        margin-bottom: 18px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        font-family: inherit;
        font-size: 14px;
        color: #334155;
        background: white;
    }

    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
    }

    .form-help {
        color: #64748b;
        font-size: 12px;
        margin-top: 7px;
        line-height: 1.6;
    }

    .date-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .item-list {
        display: grid;
        gap: 12px;
    }

    .item-row {
        display: grid;
        grid-template-columns: 1fr 120px;
        align-items: center;
        gap: 15px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }

    .item-name {
        color: #1e293b;
        font-size: 14px;
        font-weight: 700;
    }

    .item-category {
        display: block;
        color: #64748b;
        font-size: 12px;
        margin-top: 5px;
    }

    .item-stock {
        display: block;
        color: #2563eb;
        font-size: 12px;
        margin-top: 5px;
    }

    .item-row input {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 25px;
        flex-wrap: wrap;
    }

    @media(max-width: 600px) {
        .date-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .item-row {
            grid-template-columns: 1fr 90px;
            gap: 10px;
            padding: 12px;
        }

        .card {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
    <div class="page-heading">
        <h1>Form Pengajuan Peminjaman</h1>
        <p>
            Pilih peralatan yang ingin dipinjam, tentukan jumlah,
            dan lengkapi informasi peminjaman.
        </p>
    </div>

    <form
        action="{{ route('peminjaman.store') }}"
        method="POST"
        class="card form-card"
    >
        @csrf

        <h2 class="form-section-title">1. Pilih Peralatan</h2>

        @if($alats->isEmpty())
            <div class="alert alert-error">
                Belum ada alat dengan stok tersedia.
                Silakan kembali ke katalog nanti.
            </div>
        @else
            <p class="form-help" style="margin-bottom: 15px;">
                Masukkan jumlah alat yang ingin dipinjam.
                Isi 0 jika tidak ingin memilih alat tersebut.
            </p>

            <div class="item-list">
                @foreach($alats as $alat)
                    <div class="item-row">
                        <div>
                            <span class="item-name">
                                {{ $alat->name }}
                            </span>

                            <span class="item-category">
                                Kategori:
                                {{ $alat->category?->name ?? 'Tanpa kategori' }}
                            </span>

                            <span class="item-stock">
                                Stok tersedia: {{ $alat->stok }}
                            </span>
                        </div>

                        <div>
                            <label
                                for="item-{{ $alat->id }}"
                                style="font-size: 12px; color: #64748b;"
                            >
                                Jumlah
                            </label>

                            <input
                                type="number"
                                id="item-{{ $alat->id }}"
                                name="items[{{ $alat->id }}]"
                                min="0"
                                max="{{ $alat->stok }}"
                                value="{{ old('items.' . $alat->id, 0) }}"
                            >
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">

        <h2 class="form-section-title">2. Informasi Peminjaman</h2>

        <div class="date-grid">
            <div class="form-group">
                <label for="tanggal_pinjam">Tanggal Pinjam *</label>

                <input
                    type="date"
                    id="tanggal_pinjam"
                    name="tanggal_pinjam"
                    class="form-control"
                    min="{{ date('Y-m-d') }}"
                    value="{{ old('tanggal_pinjam') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="tanggal_rencana_kembali">
                    Tanggal Rencana Kembali *
                </label>

                <input
                    type="date"
                    id="tanggal_rencana_kembali"
                    name="tanggal_rencana_kembali"
                    class="form-control"
                    min="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                    value="{{ old('tanggal_rencana_kembali') }}"
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Catatan (Opsional)</label>

            <textarea
                id="notes"
                name="notes"
                class="form-control"
                rows="4"
                maxlength="1000"
                placeholder="Tuliskan keperluan atau informasi tambahan..."
            >{{ old('notes') }}</textarea>
        </div>

        <div class="alert" style="background: #eff6ff; color: #1e40af;">
            <strong>Informasi:</strong>
            Pengajuan yang dikirim akan berstatus Menunggu.
            Stok alat baru akan berkurang setelah pengajuan disetujui Petugas.
        </div>

        <div class="form-actions">
            <a
                href="{{ route('peminjaman.katalog') }}"
                class="btn btn-secondary"
            >
                Batal
            </a>

            <button
                type="submit"
                class="btn"
                {{ $alats->isEmpty() ? 'disabled' : '' }}
            >
                Kirim Pengajuan
            </button>
        </div>
    </form>
@endsection

@push('styles')
<style>
    button:disabled {
        opacity: .5;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
    const tanggalPinjam = document.getElementById('tanggal_pinjam');
    const tanggalKembali = document.getElementById('tanggal_rencana_kembali');

    tanggalPinjam?.addEventListener('change', function () {
        tanggalKembali.min = this.value;

        if (tanggalKembali.value && tanggalKembali.value < this.value) {
            tanggalKembali.value = this.value;
        }
    });
</script>
@endpush
