/* سرویسورکر اعلانهای مهراد سام */
self.addEventListener('install', function (e) { self.skipWaiting(); });
self.addEventListener('activate', function (e) { e.waitUntil(self.clients.claim()); });

self.addEventListener('push', function (e) {
    var data = {};
    try { data = e.data.json(); } catch (_) { try { data = { body: e.data.text() }; } catch (_2) {} }
    var title = data.title || 'مهراد سام';
    var opts = {
        body: data.body || '',
        icon: data.icon || '/ghaleb/manabe/favicon.png',
        badge: '/ghaleb/manabe/favicon.png',
        dir: 'rtl',
        lang: 'fa',
        tag: 'mehrsam-' + Date.now(),
        data: { url: data.url || '/mod/chat' }
    };
    e.waitUntil(self.registration.showNotification(title, opts));
});

self.addEventListener('notificationclick', function (e) {
    e.notification.close();
    var url = (e.notification.data && e.notification.data.url) || '/mod/chat';
    e.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
            for (var i = 0; i < list.length; i++) {
                if (list[i].url.indexOf(url) !== -1 && 'focus' in list[i]) return list[i].focus();
            }
            return self.clients.openWindow(url);
        })
    );
});
