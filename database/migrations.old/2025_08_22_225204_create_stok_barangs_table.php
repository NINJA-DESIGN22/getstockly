<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stok_barang', function (Blueprint $table) {
            $table->id();
            $table->string('scan_barcode');
            $table->string('sku');
            $table->string('variant')->nullable();
            $table->string('kategori');
            $table->integer('jumlah')->default(0);
            $table->integer('minimum_stok')->default(0);
            $table->decimal('harga', 14, 2)->default(0);
            $table->string('foto')->nullable();
            $table->string('username')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('jam', 5)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('stok_barang');
    }
};