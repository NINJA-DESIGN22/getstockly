<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    protected $table = 'stok_barang';
    public $timestamps = false;

    protected $fillable = [
        'foto','scan_barcode','sku','variant','nama_produk','kategori',
        'jumlah','minimum_stok','harga','username','tanggal','jam',
    ];
}
