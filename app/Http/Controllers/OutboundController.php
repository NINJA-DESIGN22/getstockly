<?php

namespace App\Http\Controllers;

use App\Models\StokBarang;
use App\Models\StokKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutboundController extends Controller
{
    public function index()
    {
        // Riwayat stok keluar
        $stokKeluar = StokKeluar::orderByDesc('id')->paginate(20);
        return view('outbound', compact('stokKeluar'));
    }

    public function store(Request $request)
    {
        // Validasi
        $data = $request->validate([
            'scan_barcode' => ['nullable','string','max:255'], // boleh kosong, akan fallback SKU
            'sku'          => ['required','string','max:255'],
            'variant'      => ['nullable','string','max:255'],
            'nama_produk'  => ['nullable','string','max:255'],
            'kategori'     => ['nullable','string','max:255'],
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

        $data['jumlah'] = (int) $data['jumlah'];
        $data['harga']  = (float) $data['harga'];

        $user = Auth::user();
        $username = $user?->username ?? $user?->name ?? $user?->email ?? 'user';

        try {
            DB::transaction(function () use ($data, $username) {

                // 1) Ambil stok master: barcode → fallback SKU (pakai lock)
                $item = null;
                if (!empty($data['scan_barcode'])) {
                    $item = StokBarang::where('scan_barcode', $data['scan_barcode'])
                        ->lockForUpdate()->first();
                }
                if (!$item) {
                    $item = StokBarang::where('sku', $data['sku'])
                        ->lockForUpdate()->first();
                }

                if (!$item) {
                    abort(422, 'Barang tidak ditemukan di stok gudang.');
                }
                if ($item->jumlah < $data['jumlah']) {
                    abort(422, 'Stok tidak mencukupi.');
                }

                // 2) Isi field transaksi dari master jika input kosong
                $variant    = $data['variant']     ?? $item->variant;
                $namaProduk = $data['nama_produk'] ?? $item->nama_produk;
                $kategori   = $data['kategori']    ?? $item->kategori;
                $harga      = $data['harga'] ?: (float) $item->harga; // pakai input, fallback harga master

                // 3) Catat ke stok_keluar (total_harga pakai generated column, jadi tidak perlu diisi)
                StokKeluar::create([
                    'scan_barcode' => $item->scan_barcode,  // pastikan konsisten dengan master
                    'sku'          => $item->sku,
                    'variant'      => $variant,
                    'nama_produk'  => $namaProduk,
                    'kategori'     => $kategori,
                    'jumlah'       => $data['jumlah'],
                    'harga'        => $harga,
                    'username'     => $username,
                    'tanggal'      => $data['tanggal'],
                    'jam'          => $data['jam'],
                ]);

                // 4) Kurangi stok gudang
                $item->jumlah -= $data['jumlah'];
                $item->save();
            });

            return redirect()->route('outbound.index')->with('status', 'Stok berhasil dikurangi!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengurangi stok: '.$e->getMessage())->withInput();
        }
    }
}
