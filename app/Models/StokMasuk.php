<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    protected $table = 'stok_masuk';
    public $timestamps = false;

    protected $fillable = [
        'foto','scan_barcode','sku','variant','nama_produk','kategori',
        'jumlah','minimum_stok','harga','username','tanggal','jam','synced_to_stock',
    ];

    protected $casts = [
        'synced_to_stock' => 'boolean',
    ];
}
