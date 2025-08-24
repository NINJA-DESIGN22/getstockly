<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stok_barang', function (Blueprint $table) {
            $table->id();
            $table->string('scan_barcode');
            $table->string('sku');
            $table->string('variant')->nullable();
            $table->string('kategori');
            $table->unsignedInteger('jumlah');          // qty masuk
            $table->unsignedInteger('minimum_stok')->default(0);
            $table->unsignedBigInteger('harga');       // simpan harga sebagai integer (rupiah)
            $table->string('foto')->nullable();        // path storage
            $table->string('username')->nullable();    // siapa yang input
            $table->date('tanggal');
            $table->time('jam');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_barang');
    }
};
