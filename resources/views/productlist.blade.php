{{-- resources/views/productlist.blade.php --}}
@extends('layout.app')

@section('title','Product List')

@push('head')
<style>
  :root{ --gap-main: 25px; }
  .page{ margin-left: calc(var(--sb-w, 180px) + var(--gap-main)); padding:6px; }

  .box{ background:#ffffff; border:5px solid #e5e7eb; border-radius:20px; padding:10px; color:#111827; }
  html.dark .box{ background:#1f2937; border-color:#0062ff49 !important; color:#ffffff; }

  .title{ margin:0 0 10px; font-weight:700; font-size:20px; }

  .tools{ display:flex; align-items:center; gap:10px; flex-wrap:nowrap; overflow:auto; padding-bottom:2px; }
  .tools form{ display:flex; align-items:center; gap:8px; margin:0; white-space:nowrap; }
  .btn-ghost{ border:2px solid #e5e7eb; background:#fff; padding:6px 10px; border-radius:10px; cursor:pointer; font-size:13px; text-decoration:none; display:inline-block; }
  .file-input{ padding:4px; border:2px solid #e5e7eb; border-radius:10px; max-width:160px; }
  html.dark .btn-ghost{ background:#1f2937; border-color:#0062ff49; color:#ffffff; }
  html.dark .file-input{ background:#505050; color:#ffffff; border-color:#0062ff49; }

  /* ==== Tabel ==== */
  .tbl{ width:100%; border-collapse:collapse; font-size:14px; table-layout:auto; }
  .tbl th, .tbl td{ border:1px solid #e5e7eb; padding:8px; vertical-align:middle; color:#111827; text-align:left; }
  .tbl thead th{ background:#f3f4f6; font-weight:700; }
  html.dark .tbl th, html.dark .tbl td{ border-color:#0062ff49; color:#e5e7eb; }
  html.dark .tbl thead th{ background:#111827; }
  .tbl td{ padding:6px 8px; }
  .scroll-x{ overflow:auto; }

  /* Rata kanan untuk angka */
  .tbl td:nth-child(7), /* Jumlah */
  .tbl td:nth-child(8), /* Minimum Stok */
  .tbl td:nth-child(9)  /* Harga */
  { text-align:right; }

  /* ==== Inline edit Minimum Stok (hanya di layar) ==== */
  .ms-view, .ms-edit{ display:flex; align-items:center; justify-content:space-between; gap:8px; }
  .ms-edit{ display:none; }
  .btn-edit{ padding:4px 6px; }
  .ms-input{ width:80px; padding:4px 6px; border:1px solid #e5e7eb; border-radius:6px; text-align:right; }
  html.dark .ms-input{ background:#111827; color:#e5e7eb; border-color:#0062ff49; }
  .msg-err{ font-size:12px; color:#b91c1c; margin-top:4px; }

  /* ==== PRINT ==== */
  @media print {
    @page { size: A4 portrait; margin: 6mm; }

    /* Hanya area print yang ditampilkan */
    body * { visibility:hidden; }
    #print-area, #print-area * { visibility:visible; }

    /* Matikan dekorasi/layout non-perlu */
    .page{ margin:0 !important; padding:0 !important; }
    .box{ border:0 !important; padding:0 !important; background:none !important; }

    /* Sembunyikan kontrol & elemen non-list */
    .tools, .pagination, nav, aside, header, footer,
    .no-print, .btn-edit, .ms-edit { display:none !important; }

    #print-area{ display:block !important; width:100% !important; margin:0 !important; }

    .scroll-x{ overflow:visible !important; }
    .tbl{
      width:100% !important;
      border-collapse:collapse !important;
      table-layout:auto !important;
      font-size:12pt !important;
    }
    .tbl th, .tbl td{
      border:1px solid #000 !important;
      padding:8pt 10pt !important;
      text-align:left !important;
      color:#000 !important;
      background:none !important;
      white-space:nowrap;
    }
    /* izinkan Nama Produk & Kategori bisa membungkus */
    .tbl td:nth-child(5), .tbl th:nth-child(5),
    .tbl td:nth-child(6), .tbl th:nth-child(6){
      white-space:normal !important;
    }

    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>
@endpush

@section('content')
@php
  /** @var \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator $products */
  $rows = $products ?? collect();
@endphp

<div class="page">
  <div class="box">
    {{-- ====== AREA YANG DICETAK ====== --}}
    <div id="print-area">
      <h3 class="title">Product List</h3>

      <div class="scroll-x">
        <table class="tbl">
          <thead>
            <tr>
              <th style="width:40px">ID</th>
              <th style="width:170px">Scan Barcode</th>
              <th style="width:100px">SKU</th>
              <th style="width:80px">Variant</th>
              <th>Nama Produk</th>
              <th style="width:140px">Kategori</th>
              <th style="width:90px">Jumlah</th>
              <th style="width:120px">Minimum Stok</th>
              <th style="width:120px">Harga</th>
              <th style="width:110px">Tanggal</th>
              <th style="width:80px">Jam</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $row)
              <tr data-row-id="{{ $row->id }}">
                <td>{{ $row->id }}</td>
                <td>{{ $row->scan_barcode ?? '-' }}</td>
                <td>{{ $row->sku ?? '-' }}</td>
                <td>{{ $row->variant ?? '-' }}</td>
                <td>{{ $row->nama_produk ?? '-' }}</td>
                <td>{{ $row->kategori ?? '-' }}</td>
                <td>{{ (int)($row->jumlah ?? 0) }}</td>

                {{-- Minimum Stok: layar bisa edit, print hanya angka --}}
                <td>
                  <div class="ms-view no-print">
                    <span class="ms-value">{{ (int)($row->minimum_stok ?? 0) }}</span>
                    <button type="button" class="btn-ghost btn-edit">Edit</button>
                  </div>
                  <div class="only-print" style="display:none;">
                    {{ (int)($row->minimum_stok ?? 0) }}
                  </div>

                  <form class="ms-edit" method="POST" action="{{ route('products.minimum-stock.update', $row->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_row_id" value="{{ $row->id }}">
                    <input class="ms-input" name="minimum_stok" type="number" min="0" step="1"
                           value="{{ old('_row_id') == $row->id ? old('minimum_stok', $row->minimum_stok ?? 0) : ($row->minimum_stok ?? 0) }}" required>
                    <button class="btn-ghost" type="submit" title="Simpan">✔</button>
                    <button class="btn-ghost btn-cancel" type="button" title="Batal">✖</button>
                  </form>

                  @if ($errors->has('minimum_stok') && old('_row_id') == $row->id)
                    <div class="msg-err">{{ $errors->first('minimum_stok') }}</div>
                  @endif
                </td>

                <td>{{ is_numeric($row->harga ?? null) ? number_format((float)$row->harga, 0, ',', '.') : '0' }}</td>
                <td>{{ $row->tanggal ?? '-' }}</td>
                <td>{{ $row->jam ?? '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="11" align="center">Belum ada data</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Toolbar (tidak ikut tercetak) --}}
    <div class="tools no-print" style="margin-top:10px;">
      <a class="btn-ghost" href="{{ route('products.export.excel') }}">Export Excel</a>
      <a class="btn-ghost" href="{{ route('products.export.pdf') }}">Export PDF</a>
      <button class="btn-ghost" type="button" onclick="window.print()">🖨 Print</button>

      <form method="POST" action="{{ route('products.import.excel') }}" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:center;">
        @csrf
        <input class="file-input" type="file" name="file" accept=".xlsx,.xls,.csv" required>
        <button type="submit" class="btn-ghost">Import Excel</button>
      </form>
    </div>

    @if(method_exists($rows, 'links'))
      <div class="no-print" style="margin-top:10px;">{{ $rows->links() }}</div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Toggle edit minimum stok (hanya untuk tampilan layar)
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('tr[data-row-id]').forEach(function(tr){
      const view = tr.querySelector('.ms-view');
      const form = tr.querySelector('.ms-edit');
      const btnEdit = tr.querySelector('.btn-edit');
      const btnCancel = tr.querySelector('.btn-cancel');
      const input = tr.querySelector('.ms-input');
      const valueEl = tr.querySelector('.ms-value');

      btnEdit?.addEventListener('click', function(){
        if (!view || !form) return;
        view.style.display = 'none';
        form.style.display = 'flex';
        setTimeout(()=>input?.focus(), 0);
      });

      btnCancel?.addEventListener('click', function(){
        if (!view || !form) return;
        form.style.display = 'none';
        view.style.display = 'flex';
        if (input && valueEl) input.value = (valueEl.textContent || '0').trim();
      });
    });
  });
</script>
@endpush
