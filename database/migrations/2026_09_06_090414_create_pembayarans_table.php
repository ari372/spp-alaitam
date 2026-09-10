    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('pembayaran_tagihan', function (Blueprint $table) {

                $table->id();

                $table->foreignId('tagihan_id')
                    ->constrained('tagihan')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->decimal('nominal', 15, 2);

                $table->enum('metode', [
                    'transfer',
                    'qris'
                ]);

                $table->string('bukti_pembayaran');

                $table->enum('status', [
                    'menunggu',
                    'dibayar',
                    'ditolak'
                ])->default('menunggu');

                $table->text('catatan')->nullable();

                $table->timestamp('tanggal_kirim')
                    ->nullable();

                $table->timestamp('tanggal_disetujui')
                    ->nullable();

                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('pembayaran_tagihan');
        }
    };