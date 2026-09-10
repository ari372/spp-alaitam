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
    Schema::create('siswa', function (Blueprint $table) {
        $table->id();

        $table->string('nis')->unique();
        $table->string('nama');
        $table->enum('jenis_kelamin', ['L', 'P']);
        $table->text('alamat')->nullable();

        $table->foreignId('kelas_id')
            ->constrained('kelas')
            ->cascadeOnDelete();

        $table->foreignId('orang_tua_id')
            ->constrained('orang_tua')
            ->cascadeOnDelete();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
