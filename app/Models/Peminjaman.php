<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    protected $table = 'peminjamen';

    protected $fillable = [
        'pinjam_code',
        'user_id',
        'disetujui_oleh',
        'tanggal_pinjam',
        'tanggal_rencana_kembali',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_rencana_kembali' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugasPenyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'pinjam_id');
    }

    public function pengembalians(): HasMany
    {
        return $this->hasMany(Pengembalian::class, 'peminjaman_id');
    }
}