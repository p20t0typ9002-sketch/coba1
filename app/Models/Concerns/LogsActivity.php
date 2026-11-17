<?php

namespace App\Models\Concerns;

use App\Services\ActivityLogger;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $nama = $model->getAttribute('name')
                ?? $model->getAttribute('nama')
                ?? $model->getAttribute('pinjam_code')
                ?? 'ID ' . $model->getKey();

            ActivityLogger::log(
                'data_dibuat',
                'Menambahkan data ' . class_basename($model)
                    . ': ' . $nama
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();

            $ignored = [
                'updated_at',
                'password',
                'remember_token',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ];

            $changedFields = array_diff(
                array_keys($changes),
                $ignored
            );

            if (empty($changedFields)) {
                return;
            }

            ActivityLogger::log(
                'data_diubah',
                'Mengubah data ' . class_basename($model)
                    . ' ID ' . $model->getKey()
                    . '. Kolom yang diubah: '
                    . implode(', ', $changedFields)
            );
        });

        static::deleted(function ($model) {
            ActivityLogger::log(
                'data_dihapus',
                'Menghapus data ' . class_basename($model)
                    . ' ID ' . $model->getKey()
            );
        });
    }
}
