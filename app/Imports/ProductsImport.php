<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public int $created = 0;
    public int $updated = 0;

    public function headingRow(): int
    {
        // Baris pertama = header (SKU | Variant | Nama Produk | Jumlah)
        return 1;
    }

    public function model(array $row)
    {
        // Header Excel akan jadi snake_case:
        // "SKU" -> sku, "Variant" -> variant, "Nama Produk" -> nama_produk, "Jumlah" -> jumlah
        $sku         = $this->asString($row['sku'] ?? null);
        if (!$sku) return null; // SKU wajib

        $variant     = $this->asString($row['variant'] ?? null);
        $namaProduk  = $this->asString($row['nama_produk'] ?? null);
        $jumlah      = isset($row['jumlah']) && $row['jumlah'] !== '' ? (int)$row['jumlah'] : null;

        $existing = Product::where('sku', $sku)->first();

        if ($existing) {
            // === UPDATE: hanya field yang dikirim Excel yang diubah ===
            if ($variant !== null)    $existing->variant     = $variant;
            if ($namaProduk !== null) $existing->nama_produk = $namaProduk;
            if ($jumlah !== null)     $existing->jumlah      = $jumlah;

            // cap waktu ringan
            $existing->jam = now('Asia/Jakarta')->format('H:i:s');

            $existing->save();
            $this->updated++;
            return null; // ToModel boleh return null untuk update manual
        }

        // === INSERT BARU: set default aman untuk kolom NOT NULL ===
        $model = new Product([
            'sku'           => $sku,
            'variant'       => $variant,
            'nama_produk'   => $namaProduk,
            'jumlah'        => $jumlah ?? 0,

            // default aman (SESUAIKAN dengan skema tabel kamu)
            'scan_barcode'  => null,
            'kategori'      => 'Umum', // jika kolom kategori NOT NULL
            'minimum_stok'  => 0,
            'harga'         => 0,

            'tanggal'       => now('Asia/Jakarta')->toDateString(),
            'jam'           => now('Asia/Jakarta')->format('H:i:s'),
        ]);

        $model->save();
        $this->created++;

        return $model;
    }

    private function asString($val): ?string
    {
        if ($val === null || $val === '') return null;
        if (is_string($val)) return trim($val);
        if (is_int($val))    return (string)$val;
        if (is_float($val))  return sprintf('%.0f', $val); // hindari scientific notation
        return (string)$val;
    }
}
