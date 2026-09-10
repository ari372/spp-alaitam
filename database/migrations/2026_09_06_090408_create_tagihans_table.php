<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('tagihan', function (Blueprint $table) {
        $table->id();

        $table->foreignId('siswa_id')
            ->constrained('siswa')
            ->cascadeOnDelete();

        $table->foreignId('tahun_ajaran_id')
            ->constrained('tahun_ajaran')
            ->cascadeOnDelete();

        $table->foreignId('kategori_tagihan_id')
            ->constrained('kategori_tagihan')
            ->cascadeOnDelete();

        $table->decimal('nominal', 15, 2);

        $table->date('jatuh_tempo')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
