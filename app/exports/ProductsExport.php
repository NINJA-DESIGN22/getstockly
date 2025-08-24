<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    public function collection()
    {
        return Product::select([
            'id','scan_barcode','sku','variant','nama_produk','kategori',
            'jumlah','minimum_stok','harga','username','tanggal','jam'
        ])->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID','Scan Barcode','SKU','Variant','Nama Produk','Kategori',
            'Jumlah','Minimum Stok','Harga','Username','Tanggal','Jam'
        ];
    }

    /**
     * Map tiap record -> baris Excel.
     * Pastikan kolom sensitif (barcode/sku/variant) dipaksa ke string.
     */
    public function map($p): array
    {
        return [
            (int) $p->id,
            (string) $p->scan_barcode,   // teks: tidak jadi 6.95E+12
            (string) $p->sku,            // teks agar leading zero aman
            (string) $p->variant,        // teks
            (string) $p->nama_produk,
            (string) $p->kategori,
            (int) ($p->jumlah ?? 0),
            (int) ($p->minimum_stok ?? 0),
            (float) ($p->harga ?? 0),
            (string) ($p->username ?? ''),
            (string) ($p->tanggal ?? ''), // biarkan string (format terserah kamu)
            (string) ($p->jam ?? ''),     // string
        ];
    }

    /**
     * Paksa format kolom di Excel.
     * A=ID, B=Scan Barcode, C=SKU, D=Variant, ... dst.
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,  // Scan Barcode -> teks
            'C' => NumberFormat::FORMAT_TEXT,  // SKU -> teks
            'D' => NumberFormat::FORMAT_TEXT,  // Variant -> teks
            // 'K' => NumberFormat::FORMAT_DATE_YYYYMMDD2, // Kalau mau format tanggal Excel asli
            // 'L' => NumberFormat::FORMAT_DATE_TIME4,     // Kalau mau format jam Excel asli
        ];
    }
}
