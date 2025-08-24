{{-- resources/views/products-grid.blade.php --}}
@php
  /** @var \Illuminate\Pagination\LengthAwarePaginator $products */
@endphp

@includeIf('layout.sidebar')

<style>
  :root{ --gap-main: 25px; }
  .page{ margin-left: calc(var(--sb-w) + var(--gap-main)); padding:6px; }

  .box{ background:#fff; border:5px solid #e5e7eb; border-radius:20px; padding:14px; color:#111827; }
  html.dark .box{ background:#1f2937; border-color:#0062ff49 !important; color:#e5e7eb; }

  .head{ display:flex; gap:0px; align-items:center; justify-content:space-between; margin-bottom:10px; }
  .title{ margin:0; font-weight:800; font-size:30px; }
  .search{ display:flex; gap:8px; align-items:center; }
  .search input{ width:260px; padding:8px 10px; border-radius:10px; border:1px solid #e5e7eb; background:#fff; color:#111827; }
  html.dark .search input{ background:#0f172a; border-color:#334155; color:#e5e7eb; }
  .search button{ padding:8px 12px; border-radius:10px; border:1px solid transparent; background:#2563eb; color:#fff; font-weight:700; cursor:pointer; }
  html.dark .search button{ background:#1d4ed8; }

  .grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(140px,1fr)); gap:5px; }

  .card{ background:#fff; border:2px solid #e5e7eb; border-radius:16px; overflow:hidden; display:flex; flex-direction:column; }
  html.dark .card{ background:#0f172a; border-color:#334155; }

  .pic{
  width:100%;
  aspect-ratio: 4 / 4;          /* tinggi-lebar tetap */
  background:#f3f4f6;           /* warna “letterbox” */
  display:grid;
  place-items:center;
  overflow:hidden;               /* jaga tepi rapi */
  border-top-left-radius: 10px;  /* opsional biar nyatu dg card */
  border-top-right-radius: 10px;
  }
  html.dark .pic{ background:#111827; }
  
  /* gambar tidak memaksa isi box; center, tidak merusak layout */
  .pic img{
  width:100%;
  height:100%;
  object-fit: contain;           /* <<< kunci: tidak auto-fill */
  object-position: center center;
  image-orientation: from-image; /* hormati EXIF orientasi */
  display:block;
  }

  /* isi kartu ala mockup */
  .body{ padding:0px; display:flex; flex-direction:column; gap:0px; }
  .row{ display:flex; justify-content:space-between; align-items:center; gap:8px; }
  .cat{ font-weight:700; }                  /* Kosmetik */
  .prod-link{ text-decoration:none; }       /* mascara di kanan atas */
  html.dark .prod-link{ color:#ffffff; }
  .kv{ font-size:16px; }                    /* baris kategori/nama produk */
  .pair{ font-size:12px; }
  .pair .lbl{ opacity:.9; }
  .price-line a{
    font-weight:600; text-decoration:none;
    color:#000000;font-size:14px;
  } 
  html.dark .price-line a{ color:#ffffff; }
  .stock{ font-weight:800; font-size:14px;}
  .upload-btn{
    display:inline-flex; align-items:center; gap:0px; 
    padding:6px 10px; border-radius:6px; background:#3f53ac; color:#fff; font-weight:600; font-size:10px; cursor:pointer;
  }
  .upload-btn:hover{ filter:brightness(.95); }
  html.dark .upload-btn{ background:#374c97; }
  .pagi{ margin-top:0px; }
  .alert-ok{ background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:10px; padding:8px; margin-bottom:12px; }
  .alert-err{ background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:8px; margin-bottom:12px; }
  html.dark .alert-ok{ background:#052e2b; border-color:#134e4a; color:#a7f3d0; }
  html.dark .alert-err{ background:#3f1d1d; border-color:#7f1d1d; color:#fecaca; }
</style>

<div class="page">
  <div class="box" id="gridBox">
    @if(session('status')) <div class="alert-ok">{{ session('status') }}</div> @endif
    @if($errors->any())  <div class="alert-err">{{ $errors->first() }}</div>  @endif

    <div class="head">
      <h3 class="title">Product</h3>
      <form class="search" method="get" action="{{ route('products.grid') }}">
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari Nama Produk / SKU / Kategori / Variant">
        <button type="submit">Cari</button>
      </form>
    </div>

    <div class="grid" id="grid">
      @forelse($products as $p)
        @php
          $nama = $p->nama_produk ?: '-';
          $kategori = $p->kategori ?: '-';
          $variant = $p->variant ?: '-';
        @endphp
        <div class="card" data-id="{{ $p->id }}">
          <div class="pic">
            @if($p->foto)
              <img src="{{ asset('storage/'.ltrim($p->foto,'/')) }}" alt="{{ $nama }}">
            @else
              <span style="opacity:.6;">No Image</span>
            @endif
          </div>

          <div class="body">
            <!-- baris 1: Kategori | Nama Produk -->
            <div class="row kv">
              <div class="cat">{{ ucfirst($kategori) }}</div>
              <a class="prod-link" href="javascript:void(0)">{{ $nama }}</a>
            </div>

            <!-- baris 2: SKU | Varian -->
            <div class="row pair">
              <div><span class="lbl">SKU :</span> {{ $p->sku ?? '-' }}</div>
              <div><span class="lbl">Variant :</span> {{ $variant }}</div>
            </div>

            <!-- baris 3: Harga (biru) -->
            <div class="row price-line">
              <a href="javascript:void(0)">Rp{{ number_format($p->harga ?? 0, 0, ',', '.') }}</a>
              <span></span>
            </div>

            <!-- baris 4: Stok | Upload -->
            <div class="row">
              <div class="stock">Stok : {{ $p->jumlah ?? 0 }}</div>
              <form method="POST" action="{{ route('products.photo.update', $p->id) }}" enctype="multipart/form-data">
                @csrf
                <label class="upload-btn">
                  <input type="file" name="foto" accept="image/*" hidden onchange="this.form.submit()">
                  Upload
                </label>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div style="opacity:.7;">Belum ada data</div>
      @endforelse
    </div>

    <div class="pagi">
      {{ $products->appends(['q' => $q ?? ''])->links() }}
    </div>
  </div>
</div>

{{-- Auto-refresh sederhana: cek data baru tiap 15 detik --}}
<script>
(function(){
  const GRID = document.getElementById('grid');
  if(!GRID) return;

  let latestId = Number((GRID.querySelector('.card')?.dataset.id) || 0);

  async function refresh(){
    try{
      const res = await fetch(`{{ route('products.api') }}?latest_id=${latestId}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
      const data = await res.json();
      if(data.latestId) latestId = data.latestId;

      if(data.hasNew){
        GRID.innerHTML = data.items.map(p => {
          const nama = (p.nama_produk && p.nama_produk.trim()) ? p.nama_produk : '-';
          const kategori = p.kategori ?? '-';
          const variant = p.variant ?? '-';
          const img = p.foto ? `{{ asset('storage') }}/${String(p.foto).replace(/^\//,'')}` : '';
          return `
          <div class="card" data-id="${p.id}">
            <div class="pic">${img ? `<img src="${img}" alt="${nama}">` : `<span style="opacity:.6;">No Image</span>`}</div>
            <div class="body">
              <div class="row kv">
                <div class="cat">${(kategori||'-').charAt(0).toUpperCase()+ (kategori||'-').slice(1)}</div>
                <a class="prod-link" href="javascript:void(0)">${nama}</a>
              </div>
              <div class="row pair">
                <div><span class="lbl">SKU :</span> ${p.sku ?? '-'}</div>
                <div><span class="lbl">Varian :</span> ${variant}</div>
              </div>
              <div class="row price-line">
                <a href="javascript:void(0)">Rp${Number(p.harga||0).toLocaleString('id-ID')}</a>
                <span></span>
              </div>
              <div class="row">
                <div class="stock">Stok : ${p.jumlah ?? 0}</div>
                <span style="opacity:.85;">Upload</span>
              </div>
            </div>
          </div>`;
        }).join('');
      }
    }catch(e){}
  }

  setInterval(refresh, 15000);
})();
</script>
