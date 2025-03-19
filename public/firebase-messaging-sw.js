importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "{{ config('firebase.api_key') }}",
    authDomain: "{{ config('firebase.auth_domain') }}",
    projectId: "{{ config('firebase.project_id') }}",
    messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
    appId: "{{ config('firebase.app_id') }}",
});

const messaging = firebase.messaging();

// Handle only background messages
messaging.onBackgroundMessage((payload) => {
    console.log("Received background message:", payload);
    console.log("Image URL:", payload.data?.image); // Check if image URL is present

    const imageUrl = payload.data?.image
        ? `${self.location.origin}/${payload.data.image}`
        : "/img/placeholder-img.jfif";

    console.log("Final Image URL:", imageUrl); // Verify the final URL in the console

    const notificationOptions = {
        body: payload.notification.body,
        icon: imageUrl,
        image: imageUrl
    };

    self.registration.showNotification(payload.notification.title, notificationOptions);
});

