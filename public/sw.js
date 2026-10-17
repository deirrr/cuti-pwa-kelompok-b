const CACHE_NAME = 'medika-cuti-static-v1';
const CACHE_PREFIX = 'medika-cuti-static-';
const scopeUrl = new URL(self.registration.scope);
const scopePath = scopeUrl.pathname.endsWith('/') ? scopeUrl.pathname : `${scopeUrl.pathname}/`;
const offlineUrl = new URL('offline.html', scopeUrl).href;
const precacheUrls = [
    offlineUrl,
    new URL('logo-cuti.png', scopeUrl).href,
    new URL('icons/icon-192.png', scopeUrl).href,
    new URL('icons/icon-512.png', scopeUrl).href,
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(precacheUrls))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName.startsWith(CACHE_PREFIX) && cacheName !== CACHE_NAME)
                    .map((cacheName) => caches.delete(cacheName)),
            ))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match(offlineUrl)));

        return;
    }

    const requestUrl = new URL(request.url);
    const staticDestinations = ['font', 'image', 'script', 'style'];
    const staticPathPrefixes = [`${scopePath}build/`, `${scopePath}icons/`];
    const staticFiles = [`${scopePath}logo-cuti.png`];
    const isStaticFile = staticPathPrefixes.some((path) => requestUrl.pathname.startsWith(path))
        || staticFiles.includes(requestUrl.pathname);

    if (
        requestUrl.origin !== self.location.origin
        || ! staticDestinations.includes(request.destination)
        || ! isStaticFile
    ) {
        return;
    }

    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(request).then((networkResponse) => {
                if (networkResponse.ok) {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, responseToCache));
                }

                return networkResponse;
            });
        }),
    );
});
