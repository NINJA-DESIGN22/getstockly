// public/sw.js
const CACHE = 'getstockly-v1';

// Halaman & aset yang ingin langsung tersedia offline
const PRECACHE = [
  '/',
  '/dashboard',
  '/manifest.webmanifest',
  '/icons/logo getstockly 192x192.png',
  '/icons/logo getstockly 512x512.png',
];


self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE).then((cache) => cache.addAll(PRECACHE))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.map(k => (k !== CACHE ? caches.delete(k) : null)))
    )
  );
  self.clients.claim();
});

// Strategi runtime sederhana: cache-first fallback ke network
self.addEventListener('fetch', (event) => {
  const req = event.request;
  // Abaikan permintaan non-GET (POST/PUT) agar tidak mengganggu form
  if (req.method !== 'GET') return;
  event.respondWith(
    caches.match(req).then(cached =>
      cached || fetch(req).then(resp => {
        // Simpan salinan response untuk permintaan GET yang sukses
        const respClone = resp.clone();
        caches.open(CACHE).then(cache => cache.put(req, respClone));
        return resp;
      }).catch(() => cached) // kalau offline, pakai cache bila ada
    )
  );
});
