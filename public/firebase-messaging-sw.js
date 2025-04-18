importScripts("https://www.gstatic.com/firebasejs/11.5.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/11.5.0/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "{{ config('firebase.api_key') }}",
    authDomain: "{{ config('firebase.auth_domain') }}",
    projectId: "{{ config('firebase.project_id') }}",
    messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
    appId: "{{ config('firebase.app_id') }}"
});

if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
}

self.addEventListener('message', (event) => {
    if (event.data.type === 'FIREBASE_CONFIG' && !firebase.apps.length) {
        firebase.initializeApp(event.data.config);
    }
});

// Message deduplication
let lastMessageTime = 0;
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    const now = Date.now();
    if (now - lastMessageTime < 5000) return;
    lastMessageTime = now;

    try {
        const link = payload.data?.link ? JSON.parse(payload.data.link) : {};

        const resolveImageUrl = (path) => {
            if (!path) return null;
            // Keep absolute URLs as-is
            if (path.startsWith('http')) return path;
            // Handle your public folder path format
            return `${self.location.origin}/${path.replace(/^\//, '')}`;
        };

        const imageUrl = resolveImageUrl(payload.data?.image) || '/img/placeholder-icon.png';

        const notificationOptions = {
            body: payload.notification?.body,
            icon: imageUrl,
            image: imageUrl,
            data: { url: link.web || "#" }
        };

        self.registration.showNotification(
            payload.notification?.title || "New Notification",
            notificationOptions
        );
    } catch (error) {
        console.error("Background message error:", error);
    }
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || "#";
    event.waitUntil(clients.openWindow(url));
});
