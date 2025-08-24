<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();
            $table->string('scan_barcode')->nullable();
            $table->string('sku')->index();
            $table->string('variant')->nullable();
            $table->string('nama_produk');
            $table->string('kategori');
            $table->integer('jumlah');
            $table->decimal('harga', 15, 2); // simpan rupiah aman
            $table->string('username')->nullable();
            $table->date('tanggal');
            $table->time('jam')->nullable();
            // $table->timestamps(); // aktifkan kalau mau catat created_at/updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbounds');
    }
};
