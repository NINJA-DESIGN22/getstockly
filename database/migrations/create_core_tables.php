<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* ========== SESSIONS (untuk SESSION_DRIVER=database) ========== */
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        /* ========== USER (sesuai DB kamu: nama tabel 'user') ========== */
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $t) {
                $t->id();
                $t->string('nama_lengkap');
                $t->string('username')->unique();
                $t->string('password');
                $t->string('email')->unique();
                $t->string('level')->default('admin');
                $t->string('foto')->nullable();
                $t->boolean('status_aktif')->default(1);
                $t->timestamp('tanggal_daftar')->useCurrent();
            });
        }

        /* ========== STOK BARANG (master gudang) ========== */
        if (!Schema::hasTable('stok_barang')) {
            Schema::create('stok_barang', function (Blueprint $t) {
                $t->id();
                $t->string('foto')->nullable();
                $t->string('scan_barcode')->nullable();
                $t->string('sku')->unique();
                $t->string('variant')->nullable();
                $t->string('nama_produk');
                $t->string('kategori');
                $t->integer('jumlah')->default(0);
                $t->integer('minimum_stok')->default(0);
                $t->decimal('harga', 15, 2)->default(0);
                $t->string('username')->nullable();
                $t->date('tanggal')->nullable();
                $t->time('jam')->nullable();

                $t->index(['scan_barcode']);
            });
        }

        /* ========== STOK MASUK (riwayat inbound) ========== */
        if (!Schema::hasTable('stok_masuk')) {
            Schema::create('stok_masuk', function (Blueprint $t) {
                $t->id();
                $t->string('foto')->nullable();
                $t->string('scan_barcode')->nullable();
                $t->string('sku');
                $t->string('variant')->nullable();
                $t->string('nama_produk');
                $t->string('kategori');
                $t->integer('jumlah')->default(0);
                $t->integer('minimum_stok')->default(0);
                $t->decimal('harga', 15, 2)->default(0);
                $t->string('username')->nullable();
                $t->date('tanggal');               // form kamu kirim date
                $t->time('jam');                   // form kamu kirim time
                $t->boolean('synced_to_stock')->default(false);

                $t->index(['sku', 'scan_barcode']);
            });
        }

        /* ========== STOK KELUAR (riwayat outbound) ========== */
        if (!Schema::hasTable('stok_keluar')) {
            Schema::create('stok_keluar', function (Blueprint $t) {
                $t->id();
                $t->string('foto')->nullable();
                $t->string('scan_barcode')->nullable();
                $t->string('sku');
                $t->string('variant')->nullable();
                $t->string('nama_produk');
                $t->string('kategori');
                $t->integer('jumlah')->default(0);
                $t->integer('minimum_stok')->default(0);
                $t->decimal('harga', 15, 2)->default(0);

                // pakai generated column jika MySQL 5.7+/8.0; kalau mesinmu tidak support,
                // ganti baris ini dengan: $t->decimal('total_harga', 20, 2)->default(0);
                $t->decimal('total_harga', 20, 2)->storedAs('jumlah * harga');

                $t->string('username')->nullable();
                $t->date('tanggal')->nullable();
                $t->time('jam')->nullable();

                $t->index(['sku', 'scan_barcode']);
            });
        }
    }

    public function down(): void
    {
        // drop dalam urutan aman
        Schema::dropIfExists('stok_keluar');
        Schema::dropIfExists('stok_masuk');
        Schema::dropIfExists('stok_barang');
        Schema::dropIfExists('user');
        Schema::dropIfExists('sessions');
    }
};
