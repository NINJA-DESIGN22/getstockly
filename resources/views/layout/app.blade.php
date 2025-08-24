<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">

    <title>@yield('title','Getstockly')</title>

    {{-- PWA: manifest & icons --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2563eb">

    {{-- iOS support --}}
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Getstockly">

    {{-- Fallback favicon --}}
    <link rel="icon" href="{{ asset('icons/icon-192.png') }}" sizes="192x192" type="image/png">

    {{-- Tempat inject <style>/<link> tambahan dari halaman --}}
    @stack('head')
  </head>
  <body>
    {{-- Sidebar --}}
    @includeIf('layout.sidebar')

    {{-- Area konten halaman --}}
    <main id="app">
      @yield('content')
    </main>

    {{-- Tempat inject <script> tambahan dari halaman --}}
    @stack('scripts')

    {{-- PWA: register Service Worker (harus di akhir, sebelum </body>) --}}
    <script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js')
        .then(reg => {
          console.log('✅ ServiceWorker terdaftar:', reg.scope);
        })
        .catch(err => {
          console.error('❌ ServiceWorker gagal daftar:', err);
        });
    });
  }
</script>
  </body>
</html>
