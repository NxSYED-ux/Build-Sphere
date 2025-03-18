importScripts("https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js");

// Initialize Firebase in the service worker
// Initialize Firebase in the service worker
// firebase.initializeApp({
//     apiKey: "AIzaSyA09mIJB-z44VHUBBAOq9DM-31edD3bIdQ",
//     authDomain: "heights-b33db.firebaseapp.com",
//     projectId: "heights-b33db",
//     messagingSenderId: "624912086197",
//     appId: "1:624912086197:web:df04c46beaceafe9f77afc"
// });

firebase.initializeApp({
    apiKey: "AIzaSyCLom-K30l4D54NjSbjgl_P9320RBkdbNk",
    authDomain: "final-da3c1.firebaseapp.com",
    projectId: "final-da3c1",
    messagingSenderId: "145267213050",
    appId: "1:145267213050:web:53aaf1acc58668f563ff43"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log("Received background message:", payload);
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: payload.notification.icon
    });
});
