<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — GetStockly</title>
    {{-- Vite dimatikan sementara untuk menghindari error manifest saat npm run dev belum jalan --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <style>
        /* Sedikit styling dasar agar rapi tanpa bergantung library lain */
        :root { --card-w: 380px; }
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f3f4f6;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, Noto Sans, "Apple Color Emoji","Segoe UI Emoji";
        }
        .card {
            width: min(90vw, var(--card-w));
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
            padding: 24px;
        }
        .title { font-size: 20px; font-weight: 700; margin: 8px 0 2px; color:#111827; }
        .subtitle { font-size: 13px; color:#6b7280; margin-bottom: 18px; }
        label { display:block; font-size: 13px; color:#374151; margin-bottom: 6px; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 10px 12px; border:1px solid #d1d5db; border-radius:10px; outline: none;
            background:#fff; transition: border-color .15s ease;
        }
        input[type="text"]:focus, input[type="password"]:focus { border-color:#3b82f6; }
        .row { display:flex; flex-direction: column; gap: 16px; margin: 14px 0 6px; }
        .btn {
            width: 100%; border: none; padding: 10px 14px; border-radius: 10px; font-weight: 600; cursor: pointer;
            background:#2563eb; color:#fff;
        }
        .btn:active { transform: translateY(1px); }
        .muted { font-size: 12px; color:#6b7280; text-align:center; margin-top: 10px; }
        .brand { display:flex; align-items:center; gap:10px; justify-content:center; color:#111827; }
        .brand img { width: 28px; height: 28px; border-radius: 6px; }
        .alert {
            padding:10px 12px; border-radius:10px; font-size:13px; margin-bottom:10px;
        }
        .alert-error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
        .alert-success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
        .helper { display:flex; align-items:center; justify-content:space-between; margin:8px 0 2px; }
        .helper a { font-size:12px; color:#2563eb; text-decoration:none; }
        .remember { display:flex; align-items:center; gap:8px; }
        .remember input { width:16px; height:16px; }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand">
            <img src="/favicon.ico" alt="Logo">
            <div>
                <div class="title">Masuk</div>
                <div class="subtitle">Silakan login untuk melanjutkan</div>
            </div>
        </div>

        {{-- Notifikasi sukses (mis. setelah reset password) --}}
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        {{-- Validasi error --}}
        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="row">
                <div>
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus autocomplete="username">
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                </div>
            </div>

            <div class="helper">
                <label class="remember">
                    <input type="checkbox" name="remember">
                    <span>Ingat saya</span>
                </label>
                {{-- Optional: route lupa password nanti kalau sudah ada --}}
                <a href="{{ url('/forgot-password') }}">Lupa password?</a>
            </div>

            <button type="submit" class="btn">Login</button>

            <p class="muted">
                Belum punya akun?
                <a href="{{ url('/register') }}">Daftar</a>
            </p>
        </form>
    </main>
</body>
</html>
