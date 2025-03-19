importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js");

// Initialize Firebase in the service worker
firebase.initializeApp({
    apiKey: "{{ config('firebase.api_key') }}",
    authDomain: "{{ config('firebase.auth_domain') }}",
    projectId: "{{ config('firebase.project_id') }}",
    messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
    appId: "{{ config('firebase.app_id') }}",
});

// Initialize messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log("Received background message:", payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.image || "/img/placeholder-img.jfif"
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
