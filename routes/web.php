<?php

use App\Http\Controllers\PengembalianController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PeminjamDashboardController;


Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isPeminjam()) {
            return redirect()->route('home');
        }

        return redirect('/admin');
    }

    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/alat', [PeminjamanController::class, 'index'])
        ->name('peminjaman.katalog');

    Route::get('/alat/{alat:slug}', [PeminjamanController::class, 'show'])
        ->name('peminjaman.show');

    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])
        ->name('peminjaman.create');

    Route::post('/peminjaman', [PeminjamanController::class, 'store'])
        ->name('peminjaman.store');

    Route::get('/riwayat-peminjaman', [PeminjamanController::class, 'riwayat'])
        ->name('peminjaman.riwayat');

    Route::get('/pengembalian', [PengembalianController::class, 'index'])
        ->name('pengembalian.index');

    Route::get('/pengembalian/{peminjaman}/buat', [PengembalianController::class, 'create'])
        ->name('pengembalian.create');

    Route::post('/pengembalian/{peminjaman}', [PengembalianController::class, 'store'])
        ->name('pengembalian.store');

    Route::get('/laporan', [LaporanController::class, 'index'])
        ->name('laporan.index');

    Route::get('/laporan/export/excel', [LaporanExportController::class, 'excel'])
    ->name('laporan.export.excel');

    Route::get('/laporan/export/pdf', [LaporanExportController::class, 'pdf'])
        ->name('laporan.export.pdf');

    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    Route::get('/beranda', [PeminjamDashboardController::class, 'index'])
        ->name('home');
});
