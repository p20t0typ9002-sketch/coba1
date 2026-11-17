
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamen', function (Blueprint $table) {
            $table->id();

            $table->string('pinjam_code', 30)->unique();

            // Pengguna yang mengajukan peminjaman
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Petugas atau admin yang menyetujui peminjaman
            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('tanggal_pinjam');

            $table->date('tanggal_rencana_kembali');

            $table->enum('status', [
                'menunggu',
                'ditolak',
                'dipinjam',
                'selesai',
            ])->default('menunggu');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('tanggal_pinjam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
