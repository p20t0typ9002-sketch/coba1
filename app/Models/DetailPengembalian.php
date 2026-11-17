<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengembalian extends Model
{
    protected $table = 'detail_pengembalians';

    protected $fillable = [
        'pengembalian_id',
        'detail_pinjam_id',
        'jumlah_kembali',
        'kondisi_alat',
        'denda',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_kembali' => 'integer',
            'denda' => 'decimal:2',
        ];
    }

    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(Pengembalian::class, 'pengembalian_id');
    }

    public function detailPeminjaman(): BelongsTo
    {
        return $this->belongsTo(DetailPeminjaman::class, 'detail_pinjam_id');
    }
}