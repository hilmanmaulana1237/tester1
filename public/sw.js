const CACHE_NAME = 'taskapp-v2';
const OFFLINE_URL = '/offline.html';

// Assets to cache on install (only static, no-auth-required assets)
const PRECACHE_ASSETS = [
  '/offline.html',
  '/favicon.ico',
  '/favicon.svg'
];

// Install event - cache essential assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[SW] Precaching assets');
        return Promise.allSettled(
          PRECACHE_ASSETS.map(url => cache.add(url).catch(err => console.warn('[SW] Failed to cache:', url, err)))
        );
      })
      .then(() => self.skipWaiting())
  );
});

// Activate event - clean up old caches and claim clients immediately
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => {
            console.log('[SW] Deleting old cache:', name);
            return caches.delete(name);
          })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event - very conservative: only cache static assets
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // SKIP everything that is not a GET request (Livewire uses POST)
  if (event.request.method !== 'GET') {
    return;
  }

  // SKIP admin, filament, livewire, broadcasting, and API routes entirely
  if (url.pathname.startsWith('/admin') ||
      url.pathname.startsWith('/filament') ||
      url.pathname.startsWith('/livewire') ||
      url.pathname.startsWith('/broadcasting') ||
      url.pathname.startsWith('/api')) {
    return;
  }

  // SKIP all navigation requests (HTML pages) - let the server handle them fresh
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => caches.match(OFFLINE_URL))
    );
    return;
  }

  // Only cache static assets (CSS, JS, images, fonts)
  if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|woff2?|ico)$/)) {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, responseClone);
            });
          }
          return response;
        })
        .catch(() => caches.match(event.request))
    );
    return;
  }

  // Everything else: network only, no caching
});

// Handle push notifications
self.addEventListener('push', (event) => {
  const data = event.data?.json() ?? {};

  const options = {
    body: data.body || 'Ada update baru!',
    icon: '/images/icons/icon-192x192.png',
    badge: '/images/icons/icon-72x72.png',
    vibrate: [100, 50, 100],
    data: {
      url: data.url || '/dashboard'
    }
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'TaskApp', options)
  );
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  event.waitUntil(
    clients.openWindow(event.notification.data.url || '/dashboard')
  );
});
