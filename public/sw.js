/**
 * Netafarm - Service Worker
 * Progressive Web App Caching & Offline Capabilities
 */

const CACHE_NAME = 'netafarm-pwa-v1.0.0';
const OFFLINE_URL = '/offline';

const PRECACHE_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    '/images/netafarm-logo.png',
    '/favicon.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/icons/icon-maskable-192x192.png',
    '/images/icons/icon-maskable-512x512.png',
    '/apple-touch-icon.png'
];

// Install Event - Precache critical shell & offline page
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[PWA SW] Precache partial error:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Activate Event - Clean up obsolete caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Only handle GET requests; never intercept POST, PUT, DELETE
    if (request.method !== 'GET') {
        return;
    }

    // Ignore non-http/https schemes (like chrome-extension:)
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // Dynamic API endpoints that should not be cached (notifications polling, tracking heartbeat)
    if (url.pathname.startsWith('/notifications') || url.pathname.startsWith('/api/tracking')) {
        return;
    }

    // Strategy 1: Static assets (build assets, icons, fonts, images) -> Cache-First
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.ico') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js')
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    // If image fails, return transparent placeholder if needed
                    return caches.match('/images/netafarm-logo.png');
                });
            })
        );
        return;
    }

    // Strategy 2: Navigation / HTML pages -> Network-First with Cache fallback and Offline Page
    if (request.mode === 'navigate' || (request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // Try to match the exact page from cache
                    return caches.match(request).then((cachedResponse) => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        // Fallback to the dedicated offline page
                        return caches.match(OFFLINE_URL);
                    });
                })
        );
        return;
    }

    // Default: Network with cache fallback
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
