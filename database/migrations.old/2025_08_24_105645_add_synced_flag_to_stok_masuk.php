<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_synced_flag_to_stok_masuk.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('stok_masuk', function (Blueprint $table) {
            if (!Schema::hasColumn('stok_masuk', 'synced_to_stock')) {
                $table->boolean('synced_to_stock')->default(false)->after('jam');
            }
            // index kecil biar filter cepat
            $table->index('synced_to_stock', 'idx_stok_masuk_synced');
            $table->index('sku', 'idx_stok_masuk_sku');
            $table->index('scan_barcode', 'idx_stok_masuk_barcode');
        });
    }

    public function down(): void {
        Schema::table('stok_masuk', function (Blueprint $table) {
            $table->dropIndex('idx_stok_masuk_synced');
            $table->dropIndex('idx_stok_masuk_sku');
            $table->dropIndex('idx_stok_masuk_barcode');
            $table->dropColumn('synced_to_stock');
        });
    }
};

