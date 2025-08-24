<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_timestamps_to_stok_barang.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stok_barang', function (Blueprint $table) {
            $table->timestamps(); // menambah created_at & updated_at (nullable)
        });
    }

    public function down(): void
    {
        Schema::table('stok_barang', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
