importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js");

// Initialize Firebase in the service worker
firebase.initializeApp({
    apiKey: "AIzaSyCLom-K30l4D54NjSbjgl_P9320RBkdbNk",
    authDomain: "final-da3c1.firebaseapp.com",
    projectId: "final-da3c1",
    messagingSenderId: "145267213050",
    appId: "1:145267213050:web:53aaf1acc58668f563ff43"
});

// Initialize messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log("Received background message:", payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.image || "/default-icon.png"
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
