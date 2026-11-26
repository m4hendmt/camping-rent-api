<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_alat', 100);
            $table->unsignedInteger('jumlah');
            $table->date('tanggal_sewa');
            $table->date('tanggal_kembali');
            $table->unsignedInteger('total_harga');
            $table->enum('status', ['disewa', 'selesai', 'batal'])->default('disewa');
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};