<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_pembayaran', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('tanggal_jatuh_tempo')
                ->default(5);

            $table->unsignedTinyInteger('hari_pengingat')
                ->default(3);

            $table->boolean('aktif')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pembayaran');
    }
};