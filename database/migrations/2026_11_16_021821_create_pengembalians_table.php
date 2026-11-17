
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peminjaman_id')
                ->constrained('peminjamen')
                ->restrictOnDelete();

            // Petugas yang menerima pengembalian
            $table->foreignId('diterima_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('tanggal_kembali');

            $table->enum('status', [
                'diajukan',
                'diterima',
                'ditolak',
            ])->default('diajukan');

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index(['peminjaman_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalians');
    }
};
