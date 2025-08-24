{{-- resources/views/layout/sidebar.blade.php --}}
@php
  $isActive = $isActive ?? fn ($p) => request()->routeIs($p) || request()->is($p);
  $u = auth()->user();
  $nm = $u->nama_lengkap ?? $u->username ?? 'User';
  $initial = strtoupper(substr($u->username ?? 'U', 0, 1));
  $fotoUrl = $u->foto ? asset('storage/avatars/'.$u->foto) : null;
@endphp

<style>
  /* ===== Palet (tetap seperti punyamu) ===== */
  #sidebar{ background:#ffffff00; color:#111827; border:5px solid #00000023; position: fixed}
  #sidebar .muted{ color:#6b7280; }
  #sidebar a.item{ color:#111827; }
  #sidebar a.item:hover{ background:#d8e9ff; }
  #sidebar a.item.active{ background:#eef2ff; border-color:#d8e9ff;; }

  html.dark #sidebar{ background:#1f2937; color:#e5e7eb; border:5px solid #0062ff49; }
  html.dark #sidebar .muted{ color:#9ca3af; }
  html.dark #sidebar a.item{ color:#e5e7eb; }
  html.dark #sidebar a.item:hover{ background:#111827; }
  html.dark #sidebar a.item.active{ background:#0f172a; border-color:#717272; }

  /* ===== Dimensi & animasi ===== */
  :root{ --sb-w: 180px; --sb-w-collapsed: 66px; }
  #sidebar{
    position:fixed; top:14px; left:16px;
    width:var(--sb-w); height:calc(100vh - 36px);
    border-radius:22px; box-shadow:0 20px 60px rgba(0,0,0,.18);
    overflow:hidden; display:flex; flex-direction:column; z-index:60;
    transition: width .22s ease;
  }

  /* Brand bar */
  #sidebar .brand{
    display:flex; align-items:center; gap:4px;
    padding:10px 12px;
    border-bottom:3px solid #e5e7eb;
  }
  html.dark #sidebar .brand{ border-bottom:3px solid #0062ff49; }
  #sidebar .brand img{ padding:0px 6px; width:28px; height:28px; border-radius:6px; }
  #sidebar .brand strong{
    font-weight:800; letter-spacing:.0px; white-space:nowrap;
    font-size:18px; font-family: 'Poppins', sans-serif; color:#c49540
  }

  /* Header user */
  #sidebar header{ padding:12px 10px 6px 12px; }
  #sidebar .user{ display:flex; align-items:center; gap:0px; }
  #sidebar .avatar{
    width:40px; height:40px; border-radius:10px; display:grid; place-items:center;
    background:#818181; color:#fff; font-weight:700; margin-left:0px; border:1px solid transparent;
    overflow:hidden;
  }
  #sidebar .uname {
  margin-left: 6px; padding: 0; gap: 4px;
}
  /* tombol collapse */
  #sbCollapse{
    display:flex; padding: 0px 20px; margin:0px 0px; border:none; background:transparent; cursor:pointer;
    width:18px; height:16px; border-radius:10px; display:grid; place-items:center;
    color:inherit; opacity:.85; text-indent: 20px;
  }
  /* Home pill */
  #sidebar .home-pill{
    display:flex; align-items:center; gap:12px; margin:1px 0px -14px;
    padding:10px 14px; border-radius:12px; border:2px solid  #b4d5ff;
    background:#eff6ff; color:#1e3a8a; text-decoration:none;
  }
  html.dark #sidebar .home-pill{ border-color:#1e3a8a; background:#111827; color:#e2e8f0; }
  #sidebar .home-pill svg{ width:18px; height:18px; }

  /* Menu */
  #sidebar nav{ padding:6px 8px 10px; overflow:auto; }
  #sidebar a.item{
    display:flex; align-items:center; gap:12px;
    padding:10px 12px; margin:4px 6px; border-radius:12px;
    border:1px solid transparent; text-decoration:none; white-space:nowrap;
  }
  #sidebar a.item svg{ width:18px; height:18px; flex:0 0 18px; }
  #sidebar .sep{ height:1px; background:#e5e7eb; margin:8px 12px; }
  html.dark #sidebar .sep{ background:#334155; }

  /* Footer & switch */
  #sidebar .footer{ margin-top:auto; padding:10px 12px 12px; border-top:1px solid #e5e7eb; }
  html.dark #sidebar .footer{ border-top:1px solid #334155; }
  .row{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:8px 6px; }
  .sw{ position:relative; width:42px; height:24px; }
  .sw input{ display:none; }
  .sw i{ position:absolute; inset:0; border-radius:999px; background:#e5e7eb; }
  .sw i::after{ content:""; position:absolute; top:2px; left:2px; width:20px; height:20px; border-radius:50%; background:#fff; transition:.18s; }
  .sw input:checked + i{ background:#999999; }
  .sw input:checked + i::after{ transform:translateX(18px); }

  /* ====== MODE COLLAPSED (rail) ====== */
  #sidebar .section-title{
    display:flex; align-items:center; gap:12px; margin:1px 0px 0px;
    padding:10px 12px; border-radius:12px; border:2px solid #b4d5ff;
    background:#eff6ff; color:#1e3a8a; text-decoration:none;
  }
  #sidebar .section-title svg{ width:18px; height:18px; }
  html.dark #sidebar .section-title{ border-color:#1e3a8a; background:#111827; color:#e2e8f0; }

  body.sb-collapsed #sbCollapse{ display:none; }
  body.sb-collapsed #sidebar{ width:var(--sb-w-collapsed); }

  body.sb-collapsed #sidebar .brand strong{ display:none; }
  body.sb-collapsed #sidebar header .user div:not(.avatar){ display:none; }
  body.sb-collapsed #sidebar .home-pill span{ display:none; visibility: hidden;}
  body.sb-collapsed #sidebar .home-pill{ justify-content:center; padding:8px; }

  body.sb-collapsed #sidebar .section-title span{ display:none; visibility: hidden; }
  body.sb-collapsed #sidebar .section-title{ justify-content:center; padding:8px; }
  body.sb-collapsed #sidebar a.item span{ display:none; visibility: hidden;}
  body.sb-collapsed #sidebar a.item{ justify-content:center; padding:10px; margin:6px 8px; }
  body.sb-collapsed #sidebar .sep{ display:none; }

  body.sb-collapsed #sidebar .footer .row span{ display:none; }
  body.sb-collapsed #sidebar .footer{ padding:10px 6px 12px; }

  /* ==== Profile dropdown ==== */
  #sidebar .profile-wrap{ position:relative; margin-left:40px; }
  #sidebar .profile-btn{
    border:none; background:transparent; cursor:pointer;
    width:10px; height:18px; border-radius:10px; display:grid; place-items:center;
    opacity:1; color:inherit;
  }
  html.dark #sidebar .profile-btn{ color:#e5e7eb; }
  #sidebar .profile-btn:hover{ background:rgba(0,0,0,.06); }
  html.dark #sidebar .profile-btn:hover{ background:#0f172a; }

  #sidebar .profile-menu{
    position:absolute; top:44px; right:-26px; min-width:165px;
    background:var(--menu-bg, #fff); border:1px solid #e5e7eb; border-radius:12px;
    box-shadow:0 14px 36px rgba(0,0,0,.18); padding:4px; display:none; z-index:70;
  }
  html.dark #sidebar .profile-menu{ --menu-bg:#0b1220; border-color:#334155; }
  #sidebar .profile-menu a,
  #sidebar .profile-menu button{
    width:100%; text-align:left; border:none; background:transparent; cursor:pointer;
    padding:10px 10px; border-radius:10px; color:inherit; font:inherit;
    display:flex; align-items:center; gap:10px;
  }
  #sidebar .profile-menu a:hover,
  #sidebar .profile-menu button:hover{ background:#d8e9ff; }
  html.dark #sidebar .profile-menu a:hover,
  html.dark #sidebar .profile-menu button:hover{ background:#111827; }
  #sidebar .profile-menu .sep{ height:1px; background:#e5e7eb; margin:6px; }
  html.dark #sidebar .profile-menu .sep{ background:#334155; }

  /* Logout text: light & dark */
  .logout-btn span { color:#000000; font-weight:600; }
  html.dark .logout-btn span { color:#ffffff; }
  .logout-btn:hover span { text-decoration: underline; }

  .save-text{ font-weight:600; }
  html.dark .save-text{ color:#a7f3d0; }
  /* Background & teks halaman */
body{ background:#f7f7f7; color:#111827; }
html.dark body{ background:#1a273f; color:#e5e7eb; }

/* Link */
a{ color:#2563eb; }
html.dark a{ color:#b4d5ff;}

/* Panel/kartu umum; kalau kamu pakai class bebas seperti .card/.panel */
html.dark .card, 
html.dark .panel, 
html.dark .box, 
html.dark .content-block,
html.dark .table-wrap {
  background:#1f2937 !important;
  color:#e5e7eb !important;
  border-color:#334155 !important;
}

/* Form input global */
input, select, textarea{
  background:#fff; color:#111827; border:1px solid #e5e7eb;
}
html.dark input, html.dark select, html.dark textarea{
  background:#0f172a; color:#e5e7eb; border-color:#334155;
}

/* Icon kalender & jam agar terlihat di dark mode */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator { filter: invert(0); }
html.dark input[type="date"]::-webkit-calendar-picker-indicator,
html.dark input[type="time"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity:.85; }

/* Tabel global */
table{ border-collapse:collapse; }
th, td{ border:1px solid #e5e7eb; }
thead th{ background:#f3f4f6; color:#111827; }
html.dark th, html.dark td{ border-color:#334155; color:#e5e7eb; }
html.dark thead th{ background:#111827; color:#e5e7eb; }

</style>

<aside id="sidebar" aria-label="Sidebar">
  {{-- Brand --}}
  <div class="brand">
    <img src="{{ asset('images/getstockly.png') }}" alt="GetStockly">
    <strong>GetStockly</strong>
  </div>

  <header>
    <div class="user">
      <div class="avatar">
        @if($fotoUrl)
          <img src="{{ $fotoUrl }}?v={{ time() }}" alt="Foto {{ $nm }}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
        @else
          {{ $initial }}
        @endif
      </div>
      <div>
        <div class="uname muted">{{ $nm }}</div>
        <div class="uname muted">{{ '@'.$u->username }}</div>

      </div>
      {{-- 3 titik profil (posisi tetap) --}}
      <div class="profile-wrap">
        <button class="profile-btn" id="profileBtn" title="Profile menu" aria-label="Profile menu">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
            <circle cx="12" cy="5" r="2"></circle>
            <circle cx="12" cy="12" r="2"></circle>
            <circle cx="12" cy="19" r="2"></circle>
          </svg>
        </button>

        <div class="profile-menu" id="profileMenu" aria-hidden="true">
          {{-- Form upload tersembunyi (TAMBAHAN FUNGSIONAL) --}}
          <form id="formUploadFoto" method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" style="display:none;">
            @csrf
            <input type="file" name="foto" id="inputFoto" accept="image/*">
          </form>

          {{-- Edit Foto -> pilih file (tidak mengubah layout) --}}
          <button type="button" id="btnEditFoto">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7">
              <path d="M12 5h7l-2 3h-5z"/><path d="M4 19V7a2 2 0 0 1 2-2h3l2-2h4"/>
              <rect x="4" y="8" width="16" height="11" rx="2" ry="2"/>
            </svg>
            <span>Edit Foto</span>
          </button>

          {{-- Save -> submit form (tanpa JS submit manual) --}}
          <button type="submit" id="btnSaveFoto" form="formUploadFoto" style="display:none;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
            <span class="save-text">Save</span>
          </button>

          <div class="sep"></div>

          {{-- Logout --}}
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7">
                <path d="M10 17l-5-5 5-5"/><path d="M15 12H5"/><path d="M19 21V3"/>
              </svg>
              <span>Logout</span>
            </button>
          </form>
        </div>
      </div>
    </div>
    <a href="{{ route('dashboard') }}" class="home-pill" style="margin-top:10px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M3 12l9-8 9 8"/><path d="M5 10v10h14V10"/>
      </svg>
      <span>Home</span><button id="sbCollapse" title="Collapse/Expand">☰</button>
    </a>
  </header>
  <nav>
      <a href="{{ route('products.grid') }}"
       class="item {{ request()->routeIs('products.grid') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M21 7v10l-9 4-9-4V7"/>
      </svg>
      <span>Product</span>
    </a>
    <a href="{{ route('productslist.index') }}" 
       class="item {{ $isActive('products/list*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M3 7l9-4 9 4-9 4-9-4z"/>
        <path d="M21 7v10l-9 4-9-4V7"/>
      </svg>
      <span>Product List</span>
    </a>
    <a href="{{ url('/inbound') }}" class="item {{ $isActive('inbound*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M3 12h18"/><path d="M12 5l-7 7 7 7"/>
      </svg>
      <span>Inbound</span>
    </a>
    <a href="{{ url('/outbound') }}" class="item {{ $isActive('outbound*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M3 12h18"/><path d="M12 5l7 7-7 7"/>
      </svg>
      <span>Outbound</span>
    </a>
    <a href="{{ url('/min-stock') }}" class="item {{ $isActive('min-stock*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M4 4h16v6H4zM4 14h16v6H4z"/>
      </svg>
      <span>Min Stock</span>
    </a>
    <a href="{{ url('/best-seller') }}" class="item {{ $isActive('best-seller*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M12 2l3 7h7l-5.5 4.1 2.1 6.9L12 16l-6.6 4 2.1-6.9L2 9h7z"/>
      </svg>
      <span>Best Seller</span>
    </a>
    <a href="{{ url('/best-reseller') }}" class="item {{ $isActive('best-reseller*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <circle cx="12" cy="8" r="3"/><path d="M5 20a7 7 0 0 1 14 0"/>
      </svg>
      <span>Best Reseller</span>
    </a>
    <a href="{{ url('/report') }}" class="item {{ $isActive('report*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        
      </svg>
      <span>Report</span>
    </a>
    <a href="{{ url('/reseller') }}" class="item {{ $isActive('reseller*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M8 7h8M8 12h8M8 17h8"/><path d="M3 5h18v14H3z"/>
      </svg>
      <span>Reseller</span>
    </a>
    <a href="{{ url('/finance') }}" class="item {{ $isActive('finance*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M3 20h18"/><path d="M7 16V8M12 16V4M17 16v-6"/>
      </svg>
      <span>Finance</span>
    </a>
    <a href="" class="section-title" style="margin-top:10px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      <span>Reseller Menu</span>
    </a>
    <a href="{{ url('/reseller/products') }}" class="item {{ $isActive('reseller/products*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M21 7v10l-9 4-9-4V7"/>
      </svg>
      <span>Product</span>
    </a>
    <a href="{{ url('/reseller/trooly') }}" class="item {{ $isActive('reseller/trooly*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>
      </svg>
      <span>Product List</span>
    </a>
    <a href="{{ url('/reseller/trooly') }}" class="item {{ $isActive('reseller/trooly*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <circle cx="9" cy="21" r="1"/>
        <circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      <span>Orders</span>
    </a>
    <a href="{{ url('/reseller/trooly') }}" class="item {{ $isActive('reseller/trooly*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <circle cx="9" cy="21" r="1"/>
        <circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      <span>Trolly</span>
    </a>
    <a href="{{ url('/reseller/report') }}" class="item {{ $isActive('reseller/report*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        
      </svg>
      <span>Report</span>
    </a>
    <a href="{{ url('/reseller/finance') }}" class="item {{ $isActive('reseller/finance*') ? 'active':'' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
        <path d="M3 20h18"/><path d="M7 16V8M12 16V4M17 16v-6"/>
      </svg>
      <span>Finance</span>
    </a>
  </nav>
  <div class="footer">
    <div class="row">
      <span>Dark Mode</span>
      <label class="sw">
        <input type="checkbox" id="dmSwitch">
        <i></i>
      </label>
    </div>
  </div>
</aside>
<script>
  // ===== Collapse/Expand dan persist =====
  (function(){
    const btn  = document.getElementById('sbCollapse');
    const sb   = document.getElementById('sidebar');
    const KEY  = 'sb-collapsed';

    if (localStorage.getItem(KEY) === '1') {
      document.body.classList.add('sb-collapsed');
    }

    btn?.addEventListener('click', (e) => {
      e.stopPropagation();
      const next = !document.body.classList.contains('sb-collapsed');
      document.body.classList.toggle('sb-collapsed', next);
      localStorage.setItem(KEY, next ? '1' : '0');
    });

    sb?.addEventListener('click', (e) => {
      if (document.body.classList.contains('sb-collapsed')) {
        e.preventDefault();
        e.stopPropagation();
        document.body.classList.remove('sb-collapsed');
        localStorage.setItem(KEY, '0');
      }
    }, true);
  })();

  // ===== Dark Mode sinkron =====
  (function(){
    const sw = document.getElementById('dmSwitch');
    const stored = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = stored ? stored === 'dark' : prefersDark;
    document.documentElement.classList.toggle('dark', isDark);
    if (sw) sw.checked = isDark;
    sw?.addEventListener('change', () => {
      const next = sw.checked;
      document.documentElement.classList.toggle('dark', next);
      localStorage.setItem('theme', next ? 'dark' : 'light');
    });
  })();

  // ===== Profile dropdown =====
  (function(){
    const btn = document.getElementById('profileBtn');
    const menu = document.getElementById('profileMenu');
    if(!btn || !menu) return;

    const open = () => { menu.style.display = 'block'; menu.setAttribute('aria-hidden','false'); };
    const close = () => { menu.style.display = 'none'; menu.setAttribute('aria-hidden','true'); };

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = menu.getAttribute('aria-hidden') === 'false';
      isOpen ? close() : open();
    });

    document.addEventListener('click', (e) => {
      if(menu.getAttribute('aria-hidden') === 'false' && !menu.contains(e.target) && e.target !== btn){
        close();
      }
    });

    window.addEventListener('resize', close);
  })();
</script>

<script>
  // ===== Edit Foto -> pilih file; Save -> submit form via atribut 'form' =====
  (function(){
    const input  = document.getElementById('inputFoto');
    const btnE   = document.getElementById('btnEditFoto');
    const btnS   = document.getElementById('btnSaveFoto');
    const avatar = document.querySelector('#sidebar .avatar');
    if(!input || !btnE || !btnS || !avatar) return;

    btnE.addEventListener('click', () => input.click());

    input.addEventListener('change', () => {
      if (input.files && input.files[0]) {
        const url = URL.createObjectURL(input.files[0]);
        avatar.innerHTML = '';
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'Preview foto';
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:10px;';
        avatar.appendChild(img);
        btnS.style.display = 'flex'; // tampilkan tombol Save
      }
    });
  })();
  (function(){
  const sw = document.getElementById('dmSwitch');         // checkbox Dark Mode di sidebar
  const stored = localStorage.getItem('theme');           // "dark" | "light" | null
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const isDark = stored ? stored === 'dark' : prefersDark;

  // apply saat load
  document.documentElement.classList.toggle('dark', isDark);
  if (sw) sw.checked = isDark;

  // toggle saat user klik switch
  sw?.addEventListener('change', () => {
    const next = sw.checked;
    document.documentElement.classList.toggle('dark', next);
    localStorage.setItem('theme', next ? 'dark' : 'light');
  });
})();
</script>
