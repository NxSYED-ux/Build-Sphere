async function initFirebaseMessaging() {
    try {
        if (typeof firebase === 'undefined') {
            await loadFirebaseSDK();
        }

        const firebaseConfig = window.FIREBASE_CONFIG;
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }

        const messaging = firebase.messaging();
        const registration = await navigator.serviceWorker.register('/js/appjs/firebase-messaging-sw.js');

        const permission = await Notification.requestPermission();
        if (permission === "granted") {
            const token = await messaging.getToken({
                vapidKey: window.FIREBASE_CONFIG.vapidKey,
                serviceWorkerRegistration: registration
            });
            storeFCMToken(token);
        }

        messaging.onMessage(handleForegroundMessage);

    } catch (error) {
        console.error("Firebase Messaging Error:", error);
    }
}

// Helper Functions (unchanged)
async function loadFirebaseSDK() {
    return new Promise((resolve) => {
        const script = document.createElement('script');
        script.src = "/js/firebase-app-compat.js";
        script.onload = () => {
            const messagingScript = document.createElement('script');
            messagingScript.src = "/js/firebase-messaging-compat.js";
            messagingScript.onload = resolve;
            document.head.appendChild(messagingScript);
        };
        document.head.appendChild(script);
    });
}

function storeFCMToken(token) {
    localStorage.setItem('fcmToken', token);
    const fcmInput = document.getElementById('newFcmToken');
    if (fcmInput) {
        fcmInput.value = token;
    }
    // sendTokenToServer(token); // Optional: enable if needed
}

async function sendTokenToServer(token) {
    try {
        await fetch('/api/store-fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ token })
        });
    } catch (error) {
        console.error("Token submission failed:", error);
    }
}

function handleForegroundMessage(payload) {
    if (Notification.permission === "granted") {
        new Notification(
            payload.notification?.title || "New Notification",
            {
                body: payload.notification?.body,
                icon: payload.notification?.image || '/img/placeholder-img.jfif',
                data: { url: payload.data?.link || '/' }
            }
        );
    }
}

// Make available for legacy usage
window.initFirebaseMessaging = initFirebaseMessaging;
