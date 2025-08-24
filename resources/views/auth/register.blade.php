<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — GetStockly</title>
    {{-- Matikan jika Vite belum dijalankan agar tidak error manifest --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <style>
        :root { --card-w: 420px; }
        body { min-height: 100vh; display:grid; place-items:center; background:#f3f4f6; font-family: system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,Noto Sans,"Apple Color Emoji","Segoe UI Emoji"; }
        .card { width:min(92vw,var(--card-w)); background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:24px; }
        .title { font-size:20px; font-weight:700; margin:8px 0 2px; color:#111827; }
        .subtitle { font-size:13px; color:#6b7280; margin-bottom:18px; }
        label { display:block; font-size:13px; color:#374151; margin-bottom:6px; }
        input[type="text"], input[type="email"], input[type="password"] { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px; outline:none; background:#fff; transition:border-color .15s ease; }
        input:focus { border-color:#3b82f6; }
        .grid { display:grid; gap:14px; }
        .btn { width:100%; border:none; padding:10px 14px; border-radius:10px; font-weight:600; cursor:pointer; background:#2563eb; color:#fff; margin-top:8px; }
        .btn:active { transform:translateY(1px); }
        .muted { font-size:12px; color:#6b7280; text-align:center; margin-top:10px; }
        .brand { display:flex; align-items:center; gap:10px; justify-content:center; color:#111827; }
        .brand img { width:28px; height:28px; border-radius:6px; }
        .alert { padding:10px 12px; border-radius:10px; font-size:13px; margin-bottom:10px; }
        .alert-error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
        .alert-success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand">
            <img src="{{ asset('favicon.ico') }}" alt="Logo">
            <div>
                <div class="title">Daftar Akun</div>
                <div class="subtitle">Buat akun baru untuk melanjutkan</div>
            </div>
        </div>

        {{-- Notifikasi sukses --}}
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        {{-- Error validasi dari controller --}}
        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}">
            @csrf
            <div class="grid">
                <div>
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap') }}" required autofocus>
                </div>
                <div>
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username">
                </div>
                <div>
                    <label for="email">Email (opsional)</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}">
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password">
                </div>
                <div>
                    <label for="password_confirmation">Ulangi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                </div>
            </div>
            <button type="submit" class="btn">Buat Akun</button>
            <p class="muted">Sudah punya akun? <a href="{{ url('/login') }}">Login</a></p>
        </form>
    </main>
</body>
</html>
