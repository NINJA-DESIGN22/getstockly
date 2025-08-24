<?php

namespace App\Http\Controllers;

use App\Models\StokMasuk;    // tabel stok_masuk
use App\Models\StokBarang;   // tabel stok_barang
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InboundController extends Controller
{
    public function index()
    {
        $stokMasuk = StokMasuk::orderByDesc('id')->paginate(10);
        return view('inbound', compact('stokMasuk'));
    }

    public function store(Request $request)
    {
        // --- Validasi
        $data = $request->validate([
            'scan_barcode' => ['required','string','max:255'],
            'sku'          => ['required','string','max:255'],
            'variant'      => ['nullable','string','max:255'],
            'nama_produk'  => ['required','string','max:255'],
            'kategori'     => ['required','string','max:255'],
            'jumlah'       => ['required','integer','min:1'],
            'harga'        => ['required','numeric','min:0'],
            'tanggal'      => ['required','date'],
            'jam'          => ['required','date_format:H:i'],
        ]);

        // Normalisasi ringan
        $data = array_map(function ($v) {
            if (is_string($v)) {
                $v = trim($v);
                if ($v === '') { return null; }
            }
            return $v;
        }, $data);

        $user = Auth::user();
        $username = $user?->username ?? $user?->name ?? $user?->email ?? 'user';
        $data['username'] = $username;
        $data['jumlah']   = (int) $data['jumlah'];
        $data['harga']    = (float) $data['harga'];

        try {
            DB::transaction(function () use ($data, $username) {

                // 1) Simpan ke stok_masuk (flag belum sinkron)
                /** @var \App\Models\StokMasuk $inbound */
                $inbound = StokMasuk::create($data + ['synced_to_stock' => false]);

                // 2) Update/insert ke stok_barang
                $product = null;
                if (!empty($data['scan_barcode'])) {
                    $product = StokBarang::where('scan_barcode', $data['scan_barcode'])
                        ->lockForUpdate()->first();
                }
                if (!$product) {
                    $product = StokBarang::where('sku', $data['sku'])
                        ->lockForUpdate()->first();
                }

                if ($product) {
                    $product->jumlah += $data['jumlah'];

                    // hanya timpa jika ada nilai baru (tidak null/kosong)
                    $product->variant      = $data['variant']     ?? $product->variant;
                    $product->nama_produk  = $data['nama_produk'] ?? $product->nama_produk;
                    $product->kategori     = $data['kategori']    ?? $product->kategori;
                    $product->harga        = $data['harga'] ?: $product->harga;

                    $product->username = $username;
                    $product->tanggal  = $data['tanggal'];
                    $product->jam      = $data['jam'];
                    $product->save();
                } else {
                    StokBarang::create([
                        'scan_barcode' => $data['scan_barcode'],
                        'sku'          => $data['sku'],
                        'variant'      => $data['variant'],
                        'nama_produk'  => $data['nama_produk'],
                        'kategori'     => $data['kategori'],
                        'jumlah'       => $data['jumlah'],
                        'minimum_stok' => 0,
                        'harga'        => $data['harga'],
                        'username'     => $username,
                        'tanggal'      => $data['tanggal'],
                        'jam'          => $data['jam'],
                    ]);
                }

                // 3) Tandai stok_masuk sudah tersinkron
                $inbound->synced_to_stock = true;
                $inbound->save();
            });

            return back()->with('status', 'Inbound tersimpan & stok gudang ter-update otomatis.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan inbound: '.$e->getMessage());
        }
    }

    // Sinkronisasi yang tertinggal (kalau ada stok_masuk belum di-push ke stok_barang)
    public function syncMissing()
    {
        $total = 0;

        StokMasuk::where('synced_to_stock', false)
            ->orderBy('id')
            ->chunkById(200, function ($rows) use (&$total) {
                foreach ($rows as $in) {
                    DB::transaction(function () use ($in, &$total) {

                        $product = null;
                        if (!empty($in->scan_barcode)) {
                            $product = StokBarang::where('scan_barcode', $in->scan_barcode)
                                ->lockForUpdate()->first();
                        }
                        if (!$product) {
                            $product = StokBarang::where('sku', $in->sku)
                                ->lockForUpdate()->first();
                        }

                        if ($product) {
                            $product->jumlah += (int) $in->jumlah;
                            $product->variant      = $in->variant     ?? $product->variant;
                            $product->nama_produk  = $in->nama_produk ?? $product->nama_produk;
                            $product->kategori     = $in->kategori    ?? $product->kategori;
                            $product->harga        = (float) $in->harga ?: $product->harga;
                            $product->username     = $in->username ?? $product->username;
                            $product->tanggal      = $in->tanggal  ?? $product->tanggal;
                            $product->jam          = $in->jam      ?? $product->jam;
                            $product->save();
                        } else {
                            StokBarang::create([
                                'scan_barcode' => $in->scan_barcode,
                                'sku'          => $in->sku,
                                'variant'      => $in->variant,
                                'nama_produk'  => $in->nama_produk,
                                'kategori'     => $in->kategori,
                                'jumlah'       => (int) $in->jumlah,
                                'minimum_stok' => 0,
                                'harga'        => (float) $in->harga,
                                'username'     => $in->username,
                                'tanggal'      => $in->tanggal,
                                'jam'          => $in->jam,
                            ]);
                        }

                        $in->synced_to_stock = true;
                        $in->save();
                        $total++;
                    });
                }
            });

        return back()->with('status', "Sinkronisasi selesai. {$total} baris inbound diterapkan ke stok.");
    }
}
