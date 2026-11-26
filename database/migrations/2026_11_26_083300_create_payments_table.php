<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');

            $table->string('kode_pembayaran')->unique();
            $table->decimal('jumlah_bayar', 12, 2);
            $table->enum('metode_pembayaran', ['transfer', 'cash', 'qris', 'ewallet']);
            $table->enum('status_pembayaran', ['pending', 'lunas', 'gagal'])->default('pending');
            $table->dateTime('tanggal_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
