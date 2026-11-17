<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengembalian extends Model
{
    protected $fillable = [
        'peminjaman_id',
        'diterima_oleh',
        'tanggal_kembali',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kembali' => 'date',
        ];
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function petugasPenerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPengembalian::class, 'pengembalian_id');
    }
}