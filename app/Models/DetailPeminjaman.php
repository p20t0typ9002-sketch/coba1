<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailPeminjaman extends Model
{
    protected $table = 'detail_peminjamen';

    protected $fillable = [
        'pinjam_id',
        'alat_id',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'pinjam_id');
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    public function detailPengembalians(): HasMany
    {
        return $this->hasMany(DetailPengembalian::class, 'detail_pinjam_id');
    }
}