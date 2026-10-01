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
        Schema::table('tagihan', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Bulan periode tagihan
            |--------------------------------------------------------------------------
            |
            | 1  = Januari
            | 2  = Februari
            | ...
            | 12 = Desember
            |
            */

            $table->unsignedTinyInteger('bulan')
                ->nullable()
                ->after('jatuh_tempo');


            /*
            |--------------------------------------------------------------------------
            | Tahun periode tagihan
            |--------------------------------------------------------------------------
            |
            | Contoh:
            |
            | Juli 2028     = bulan 7, tahun 2028
            | Januari 2029 = bulan 1, tahun 2029
            |
            */

            $table->unsignedSmallInteger('tahun')
                ->nullable()
                ->after('bulan');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {

            $table->dropColumn([
                'bulan',
                'tahun',
            ]);

        });
    }
};