<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Cari nama index unik di sku
        // Umumnya: 'uniq_sku' atau 'stok_masuk_sku_unique'
        // Kalau sudah tahu namanya, cukup panggil dropIndex dengan nama index tsb.
        try {
            DB::statement('ALTER TABLE stok_masuk DROP INDEX uniq_sku');
        } catch (\Throwable $e) {
            try {
                DB::statement('ALTER TABLE stok_masuk DROP INDEX stok_masuk_sku_unique');
            } catch (\Throwable $e2) {
                // biarkan kalau memang tidak ada
            }
        }
    }

    public function down(): void
    {
        // Kembalikan unique jika mau
        Schema::table('stok_masuk', function (Blueprint $table) {
            $table->unique('sku', 'uniq_sku');
        });
    }
};
