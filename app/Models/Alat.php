<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\LogsActivity;

class Alat extends Model
{

    use HasFactory, LogsActivity;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'stok',
        'kondisi',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(AlatImage::class, 'alat_id')
            ->orderBy('sort_order');
    }

    public function detailPeminjamans(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'alat_id');
    }
}