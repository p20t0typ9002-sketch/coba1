<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Alat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================
        // AKUN PENGGUNA
        // =====================================

        $users = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@peminjaman.test',
                'level' => 'admin',
            ],
            [
                'name' => 'Petugas Satu',
                'username' => 'petugas1',
                'email' => 'petugas1@peminjaman.test',
                'level' => 'petugas',
            ],
            [
                'name' => 'Petugas Dua',
                'username' => 'petugas2',
                'email' => 'petugas2@peminjaman.test',
                'level' => 'petugas',
            ],
            [
                'name' => 'David',
                'username' => 'david',
                'email' => 'peminjam1@peminjaman.test',
                'level' => 'peminjam',
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'budi',
                'email' => 'peminjam2@peminjaman.test',
                'level' => 'peminjam',
            ],
            [
                'name' => 'Siti Aminah',
                'username' => 'siti',
                'email' => 'peminjam3@peminjaman.test',
                'level' => 'peminjam',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'password' => 'password123',
                    'level' => $user['level'],
                    'email_verified_at' => now(),
                ]
            );
        }

        // =====================================
        // KATEGORI ALAT
        // =====================================

        $categories = [
            [
                'name' => 'Peralatan Elektronik',
                'slug' => 'peralatan-elektronik',
                'description' => 'Berbagai peralatan elektronik.',
            ],
            [
                'name' => 'Peralatan Komputer',
                'slug' => 'peralatan-komputer',
                'description' => 'Perangkat komputer dan pendukungnya.',
            ],
            [
                'name' => 'Peralatan Multimedia',
                'slug' => 'peralatan-multimedia',
                'description' => 'Peralatan audio dan visual.',
            ],
            [
                'name' => 'Peralatan Olahraga',
                'slug' => 'peralatan-olahraga',
                'description' => 'Peralatan untuk kegiatan olahraga.',
            ],
            [
                'name' => 'Peralatan Umum',
                'slug' => 'peralatan-umum',
                'description' => 'Peralatan umum untuk berbagai kebutuhan.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }

        // =====================================
        // DATA ALAT
        // =====================================

        $alats = [
            [
                'category' => 'peralatan-elektronik',
                'name' => 'Kamera DSLR Canon',
                'slug' => 'kamera-dslr-canon',
                'description' => 'Kamera DSLR untuk dokumentasi kegiatan.',
                'stok' => 5,
                'location' => 'Gudang A',
            ],
            [
                'category' => 'peralatan-elektronik',
                'name' => 'Proyektor Epson',
                'slug' => 'proyektor-epson',
                'description' => 'Proyektor untuk presentasi dan pembelajaran.',
                'stok' => 8,
                'location' => 'Gudang A',
            ],
            [
                'category' => 'peralatan-elektronik',
                'name' => 'Speaker Aktif',
                'slug' => 'speaker-aktif',
                'description' => 'Speaker aktif untuk kebutuhan acara.',
                'stok' => 6,
                'location' => 'Gudang B',
            ],
            [
                'category' => 'peralatan-komputer',
                'name' => 'Laptop Lenovo',
                'slug' => 'laptop-lenovo',
                'description' => 'Laptop untuk kegiatan administrasi dan pembelajaran.',
                'stok' => 10,
                'location' => 'Ruang Peralatan',
            ],
            [
                'category' => 'peralatan-komputer',
                'name' => 'Monitor LED',
                'slug' => 'monitor-led',
                'description' => 'Monitor komputer ukuran 24 inci.',
                'stok' => 12,
                'location' => 'Gudang A',
            ],
            [
                'category' => 'peralatan-multimedia',
                'name' => 'Tripod Kamera',
                'slug' => 'tripod-kamera',
                'description' => 'Tripod untuk menopang kamera.',
                'stok' => 7,
                'location' => 'Gudang B',
            ],
            [
                'category' => 'peralatan-multimedia',
                'name' => 'Microphone Wireless',
                'slug' => 'microphone-wireless',
                'description' => 'Mikrofon nirkabel untuk presentasi dan acara.',
                'stok' => 9,
                'location' => 'Gudang B',
            ],
            [
                'category' => 'peralatan-olahraga',
                'name' => 'Bola Sepak',
                'slug' => 'bola-sepak',
                'description' => 'Bola untuk kegiatan olahraga sepak bola.',
                'stok' => 15,
                'location' => 'Gudang Olahraga',
            ],
            [
                'category' => 'peralatan-olahraga',
                'name' => 'Raket Badminton',
                'slug' => 'raket-badminton',
                'description' => 'Raket untuk kegiatan olahraga badminton.',
                'stok' => 20,
                'location' => 'Gudang Olahraga',
            ],
            [
                'category' => 'peralatan-umum',
                'name' => 'Kursi Lipat',
                'slug' => 'kursi-lipat',
                'description' => 'Kursi lipat untuk kebutuhan kegiatan.',
                'stok' => 30,
                'location' => 'Gudang Umum',
            ],
        ];

        foreach ($alats as $alat) {
            $category = Category::where(
                'slug',
                $alat['category']
            )->firstOrFail();

            Alat::updateOrCreate(
                ['slug' => $alat['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $alat['name'],
                    'description' => $alat['description'],
                    'stok' => $alat['stok'],
                    'kondisi' => 'baik',
                    'location' => $alat['location'],
                    'is_active' => true,
                ]
            );
        }
    }
}