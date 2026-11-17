# Langkah Langkah Menjalankan Projek Sistem Peminjaman Alat (Laravel)

## 1. Buat Projek laravel baru dahulu
#### `composer create-project laravel/laravel sistem-peminjaman`

## 2. Konfigurasi database di .env jadi MySQL 
## 3. Install Fortify untuk Sistem Autentikasi Login & Register
#### `composer require laravel/fortify`
#### `php artisan fortify:install`
#### `Setelah itu php artisan migrate`

## 4. Install npm
#### `npm install`

## 5. Install Filament untuk Dashboard Admin & Petugas
#### `composer require filament/filament:"~5.0"`
#### `php artisan filament:install --panels`

## 6. Jalankan Project
#### `php artisan serve`
#### `npm run dev`