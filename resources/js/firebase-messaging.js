 export async function initFirebaseMessaging() {
    try {
        if (typeof firebase === 'undefined') {
            await loadFirebaseSDK();
        }

        const firebaseConfig = {
            apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
            authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
            projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
            messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
            appId: import.meta.env.VITE_FIREBASE_APP_ID
        };

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }

        const messaging = firebase.messaging();
        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

        const permission = await Notification.requestPermission();
        if (permission === "granted") {
            const token = await messaging.getToken({
                vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY,
                serviceWorkerRegistration: registration
            });
            storeFCMToken(token);
        }

        messaging.onMessage(handleForegroundMessage);

    } catch (error) {
        console.error("Firebase Messaging Error:", error);
    }
}

// Helper Functions
async function loadFirebaseSDK() {
    return new Promise((resolve) => {
        const script = document.createElement('script');
        script.src = "https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js";
        script.onload = () => {
            const messagingScript = document.createElement('script');
            messagingScript.src = "https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js";
            messagingScript.onload = resolve;
            document.head.appendChild(messagingScript);
        };
        document.head.appendChild(script);
    });
}

function storeFCMToken(token) {
    localStorage.setItem('fcmToken', token);
    document.getElementById('newFcmToken').value = token;
    // sendTokenToServer(token);
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
