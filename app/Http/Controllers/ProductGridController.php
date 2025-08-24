<?php

namespace App\Http\Controllers;

use App\Models\StokBarang as Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductGridController extends Controller
{
    // Halaman grid
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $products = Product::when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('sku', 'like', "%{$q}%")
                       ->orWhere('kategori', 'like', "%{$q}%")
                       ->orWhere('variant', 'like', "%{$q}%")
                       ->orWhere('nama_produk', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(24);

       // app/Http/Controllers/ProductGridController.php
return view('productsgrid', compact('products', 'q'));

    }

    // API untuk auto-refresh grid (ambil 24 terbaru)
    public function api(Request $request)
    {
        $latest = (int) $request->get('latest_id', 0);

        $items = Product::orderByDesc('id')
            ->take(24)
            ->get([
                'id','scan_barcode','sku','nama_produk','variant','kategori',
                'jumlah','minimum_stok','harga','foto','tanggal','jam','username'
            ]);

        return response()->json([
            'hasNew'   => $latest > 0 ? ($items->max('id') > $latest) : false,
            'latestId' => $items->max('id') ?? 0,
            'items'    => $items,
        ]);
    }

    // (Opsional) Update foto dari grid
    public function updatePhoto(Request $request, Product $product)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($product->foto) {
            Storage::disk('public')->delete($product->foto);
        }

        $path = $request->file('foto')->store('produk', 'public');
        $product->update(['foto' => $path]);

        return back()->with('status', 'Foto produk diperbarui.');
    }
}
