// Use importScripts for service worker compatibility
importScripts("https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js");

// Initialize Firebase
firebase.initializeApp({
    apiKey: "{{ config('firebase.api_key') }}",
    authDomain: "{{ config('firebase.auth_domain') }}",
    projectId: "{{ config('firebase.project_id') }}",
    messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
    appId: "{{ config('firebase.app_id') }}"
});

const messaging = firebase.messaging();

// Background message handler
messaging.onBackgroundMessage((payload) => {
    console.log("Background message received:", payload);

    const imageUrl = payload.data?.image
        ? `${self.location.origin}/${payload.data.image}`
        : '/img/placeholder-img.jfif';

    const notificationOptions = {
        body: payload.notification?.body || payload.data?.message,
        icon: imageUrl,
        image: imageUrl,
        data: { url: payload.data?.link || '/' }
    };

    return self.registration.showNotification(
        payload.notification?.title || payload.data?.heading || "New Notification",
        notificationOptions
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});
