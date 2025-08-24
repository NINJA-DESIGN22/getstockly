<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokKeluar extends Model
{
    protected $table = 'stok_keluar';
    public $timestamps = false;

    protected $fillable = [
        'foto','scan_barcode','sku','variant','nama_produk','kategori',
        'jumlah','minimum_stok','harga','total_harga','username','tanggal','jam',
    ];
}
