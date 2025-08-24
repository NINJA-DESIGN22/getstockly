{{-- resources/views/outbound.blade.php --}}
@php
  /** @var \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator $stokKeluar */
@endphp

@includeIf('layout.sidebar')

<style>
  :root{ --gap-main: 25px; }
  .page{ margin-left: calc(var(--sb-w) + var(--gap-main)); padding:6px; }

  .inb-wrap{ display:flex; gap:14px; align-items:flex-start; }
  .inb-form{
    flex:0 0 13%;
    background:#fff; border:5px solid #e5e7eb; border-radius:20px; padding:10px;
  }
  .inb-table{
    flex:0 87%;
    background:#fff; border:5px solid #e5e7eb; border-radius:20px; padding:10px;
    overflow:hidden;
  }

  html.dark .inb-form, html.dark .inb-table{ background:#1f2937; border-color:#0062ff49; color:#e5e7eb; }

  .inb-title{ margin:0 0 10px; font-weight:700; font-size:20px; }

  .form-row{ margin-bottom:8px; }
  .form-row label{ display:block; font-size:12px; margin-bottom:6px; color:#111827; }
  html.dark .form-row label{ color:#ffffff; }

  .form-input, .form-number, .form-date, .form-time{
    width:100%; padding:8px 10px; border-radius:10px;
    border:1px solid #e5e7eb; background:#fff; color:#111827; font-size:14px; outline:none;
  }
  html.dark .form-input, html.dark .form-number, html.dark .form-date, html.dark .form-time{
    background:#0f172a; border-color:#334155; color:#e5e7eb;
  }

  .btn-save{
    width:100%; padding:10px 12px; border-radius:10px; border:1px solid transparent;
    background:#2563eb; color:#fff; font-weight:700; cursor:pointer;
  }
  .btn-save:hover{ filter:brightness(0.95); }
  html.dark .btn-save{ background:#1d4ed8; }

  .alert-ok{ background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:10px; padding:8px; margin-bottom:12px; }
  .alert-err{ background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:8px; margin-bottom:12px; }

  .tbl{ width:100%; border-collapse:collapse; font-size:14px; }
  .tbl th, .tbl td{ border:1px solid #e5e7eb; padding:8px; text-align:center; vertical-align:top; }
  .tbl thead th{ background:#f3f4f6; font-weight:700; }
  html.dark .tbl th, html.dark .tbl td{ border-color:#0062ff49; color:#e5e7eb; }
  html.dark .tbl thead th{ background:#111827; }
</style>

<div class="page">
  <div class="inb-wrap">
    {{-- FORM --}}
    <div class="inb-form">
      <h3 class="inb-title">Outbound</h3>

      @if(session('status'))
        <div class="alert-ok">{{ session('status') }}</div>
      @endif
      @if($errors->any())
        <div class="alert-err">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('outbound.store') }}">
        @csrf
        <div class="form-row"><label>Scan Barcode</label>
          <input class="form-input" type="text" name="scan_barcode" required>
        </div>
        <div class="form-row"><label>SKU</label>
          <input class="form-input" type="text" name="sku" required>
        </div>
        <div class="form-row"><label>Variant</label>
          <input class="form-input" type="text" name="variant">
        </div>
        <div class="form-row"><label>Nama Produk</label>
          <input class="form-input" type="text" name="nama_produk">
        </div>
        <div class="form-row"><label>Kategori</label>
          <input class="form-input" type="text" name="kategori">
        </div>
        <div class="form-row"><label>Jumlah</label>
          <input class="form-number" type="number" min="1" name="jumlah" required>
        </div>
        <div class="form-row"><label>Harga</label>
          <input class="form-number" type="number" min="0" step="0.01" name="harga" required>
        </div>
        <div class="form-row"><label>Tanggal</label>
          <input class="form-date" type="date" name="tanggal" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="form-row"><label>Jam</label>
          <input class="form-time" type="time" name="jam" value="{{ date('H:i') }}" required>
        </div>
        <button type="submit" class="btn-save">Simpan</button>
      </form>
    </div>

    {{-- TABEL --}}
    <div class="inb-table">
      <h3 class="inb-title">Daftar Stok Keluar</h3>

      <div style="overflow:auto;">
        <table class="tbl">
          <thead>
            <tr>
              <th>ID</th>
              <th>Scan Barcode</th>
              <th>SKU</th>
              <th>Variant</th>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th>Jumlah</th>
              <th>Harga</th>
              <th>Total Harga</th>
              <th>Username</th>
              <th>Tanggal</th>
              <th>Jam</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stokKeluar as $row)
              <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->scan_barcode }}</td>
                <td>{{ $row->sku }}</td>
                <td>{{ $row->variant }}</td>
                <td>{{ $row->nama_produk }}</td>
                <td>{{ $row->kategori }}</td>
                <td>{{ $row->jumlah }}</td>
                <td>{{ number_format($row->harga, 0, ',', '.') }}</td>
                <td>{{ number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
                <td>{{ $row->username }}</td>
                <td>{{ !empty($row->tanggal) ? \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') : '-' }}</td>
                <td>{{ !empty($row->jam) ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="12" align="center">Belum ada data</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div style="margin-top:10px">
        {{ $stokKeluar->links() }}
      </div>
    </div>
  </div>
</div>
