
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pengembalians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengembalian_id')
                ->constrained('pengembalians')
                ->cascadeOnDelete();

            $table->foreignId('detail_pinjam_id')
                ->constrained('detail_peminjamen')
                ->restrictOnDelete();

            $table->unsignedInteger('jumlah_kembali');

            $table->enum('kondisi_alat', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
                'hilang',
            ])->default('baik');

            $table->decimal('denda', 12, 2)->default(0);

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('detail_pinjam_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengembalians');
    }
};
