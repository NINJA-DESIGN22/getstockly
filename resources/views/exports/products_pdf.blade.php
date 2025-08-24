<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Daftar Produk</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#111; }
    h3 { margin: 0 0 10px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ddd; padding:6px 8px; text-align:center; vertical-align: top; }
    th { background: #f3f4f6; font-weight: bold; }
  </style>
</head>
<body>
  <h3>Daftar Produk</h3>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Barcode</th>
        <th>SKU</th>
        <th>Variant</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Jumlah</th>
        <th>Minimum Stok</th>
        <th>Harga</th>
        <th>Tanggal</th>
        <th>Jam</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        <tr>
          <td>{{ $r->id }}</td>
          <td>{{ $r->scan_barcode ?? '-' }}</td>
          <td>{{ $r->sku ?? '-' }}</td>
          <td>{{ $r->variant ?? '-' }}</td>
          <td>{{ $r->nama_produk ?? '-' }}</td>
          <td>{{ $r->kategori ?? '-' }}</td>
          <td>{{ $r->jumlah ?? 0 }}</td>
          <td>{{ $r->minimum_stok ?? 0 }}</td>
          <td>{{ isset($r->harga) ? number_format($r->harga,0,',','.') : '0' }}</td>
          <td>{{ !empty($r->tanggal) ? \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d') : '-' }}</td>
          <td>{{ !empty($r->jam) ? \Carbon\Carbon::parse($r->jam)->timezone('Asia/Jakarta')->format('H:i') : '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
