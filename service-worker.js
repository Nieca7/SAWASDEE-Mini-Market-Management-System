self.addEventListener('install', function (e) {
  console.log('Service Worker: Installed');
  e.waitUntil(
    caches.open('sawasdee-pos-v1').then(function (cache) {
      return cache.addAll([
        '/',
        '/admin_qr_scanner.php',
        '/manifest.json',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
        'https://unpkg.com/html5-qrcode'
      ]);
    })
  );
});

self.addEventListener('fetch', function (e) {
  e.respondWith(
    caches.match(e.request).then(function (response) {
      return response || fetch(e.request);
    })
  );
});
