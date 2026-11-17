
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjamen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pinjam_id')
                ->constrained('peminjamen')
                ->cascadeOnDelete();

            $table->foreignId('alat_id')
                ->constrained('alats')
                ->restrictOnDelete();

            // Jumlah alat yang dipinjam
            $table->unsignedInteger('jumlah');

            $table->timestamps();

            $table->index('alat_id');

            // Satu jenis alat cukup dicatat sekali
            // dalam satu pengajuan peminjaman.
            $table->unique(['pinjam_id', 'alat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjamen');
    }
};
