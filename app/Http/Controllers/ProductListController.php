<?php

namespace App\Http\Controllers;

use App\Models\StokBarang;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductListController extends Controller
{
    public function index(Request $request)
    {
        // filter sederhana
        $q        = trim((string) $request->get('q', ''));
        $perPage  = (int) ($request->get('per_page', 10));
        $perPage  = $perPage > 0 && $perPage <= 200 ? $perPage : 10;

        // sorting whitelist
        $sortable = ['id','sku','nama_produk','kategori','jumlah','harga','tanggal'];
        $sort     = in_array($request->get('sort'), $sortable, true) ? $request->get('sort') : 'id';
        $dir      = strtolower($request->get('dir')) === 'asc' ? 'asc' : 'desc';

        $rows = StokBarang::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($s) use ($q) {
                    $s->where('sku', 'like', "%{$q}%")
                      ->orWhere('scan_barcode', 'like', "%{$q}%")
                      ->orWhere('nama_produk', 'like', "%{$q}%")
                      ->orWhere('kategori', 'like', "%{$q}%");
                });
            })
            ->orderBy($sort, $dir)
            ->paginate($perPage)
            ->appends([
                'q' => $q, 'per_page' => $perPage, 'sort' => $sort, 'dir' => $dir,
            ]);

        return view('productlist', ['products' => $rows]);
    }

    // Inline edit minimum_stok
    public function updateMinimumStock(Request $request, StokBarang $product)
    {
        $data = $request->validate([
            'minimum_stok' => ['required','integer','min:0'],
        ]);

        $product->update(['minimum_stok' => $data['minimum_stok']]);

        return back()->with('success', 'Minimum stok berhasil diperbarui.');
    }

    // Export Excel (mengikuti filter & sort terkini)
    public function exportExcel(Request $request)
    {
        $q    = trim((string) $request->get('q', ''));
        $sort = in_array($request->get('sort'), ['id','sku','nama_produk','kategori','jumlah','harga','tanggal'], true)
              ? $request->get('sort') : 'id';
        $dir  = strtolower($request->get('dir')) === 'asc' ? 'asc' : 'desc';

        // Pastikan ProductsExport kamu membaca query ini (atau ubah export agar pakai StokBarang)
        $export = new ProductsExport(
            StokBarang::query()
                ->when($q !== '', function ($qq) use ($q) {
                    $qq->where(function ($s) use ($q) {
                        $s->where('sku', 'like', "%{$q}%")
                          ->orWhere('scan_barcode', 'like', "%{$q}%")
                          ->orWhere('nama_produk', 'like', "%{$q}%")
                          ->orWhere('kategori', 'like', "%{$q}%");
                    });
                })
                ->orderBy($sort, $dir)
        );

        $filename = 'products_'.now('Asia/Jakarta')->format('Ymd_His').'.xlsx';
        return Excel::download($export, $filename);
    }

    // Export PDF (mengikuti filter & sort)
    public function exportPdf(Request $request)
    {
        $q    = trim((string) $request->get('q', ''));
        $sort = in_array($request->get('sort'), ['id','sku','nama_produk','kategori','jumlah','harga','tanggal'], true)
              ? $request->get('sort') : 'id';
        $dir  = strtolower($request->get('dir')) === 'asc' ? 'asc' : 'desc';

        $rows = StokBarang::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($s) use ($q) {
                    $s->where('sku', 'like', "%{$q}%")
                      ->orWhere('scan_barcode', 'like', "%{$q}%")
                      ->orWhere('nama_produk', 'like', "%{$q}%")
                      ->orWhere('kategori', 'like', "%{$q}%");
                });
            })
            ->orderBy($sort, $dir)
            ->get();

        $pdf = Pdf::loadView('exports.products_pdf', ['rows' => $rows])
                  ->setPaper('a4', 'portrait');

        return $pdf->download('products_'.now('Asia/Jakarta')->format('Ymd_His').'.pdf');
    }

    // Import Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,xls,csv','max:5120'],
        ]);

        try {
            $import = new ProductsImport(); // pastikan kelas ini pakai StokBarang
            Excel::import($import, $request->file('file'));

            return back()->with(
                'status',
                "Import selesai. Ditambah: {$import->created}, Diupdate: {$import->updated}."
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal import: '.$e->getMessage());
        }
    }
}
