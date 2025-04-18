(function() {
     if (!window.FIREBASE_CONFIG) {
        console.error("Firebase config is missing");
        return;
    }

    async function loadFirebaseSDK() {
        return new Promise((resolve) => {
            if (typeof firebase !== 'undefined') return resolve();

            const loadScript = (src) => new Promise(resolve => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                document.head.appendChild(script);
            });

            loadScript('/js/firebase-app-compat.js')
                .then(() => loadScript('/js/firebase-messaging-compat.js'))
                .then(resolve);
        });
    }

    function storeFCMToken(token) {
        localStorage.setItem('fcmToken', token);
        const fcmInput = document.getElementById('newFcmToken');
        if (fcmInput) fcmInput.value = token;
    }

    function handleForegroundMessage(payload) {
        if (Notification.permission === "granted") {
            try {
                const link = payload.data?.link ? JSON.parse(payload.data.link) : {};
                const notification = new Notification(
                    payload.notification?.title || "New Notification",
                    {
                        body: payload.notification?.body,
                        icon: payload.notification?.image || '/img/placeholder-img.jfif',
                        data: { url: link.web || "#" }
                    }
                );
                notification.onclick = () => window.open(link.web || "#", '_blank');
            } catch (error) {
                console.error("Notification error:", error);
            }
        }
    }

    async function initFirebaseMessaging() {
        try {
            await loadFirebaseSDK();

            if (!firebase.apps.length) {
                firebase.initializeApp(window.FIREBASE_CONFIG);
            }

            const registration = await navigator.serviceWorker.register(
                '/firebase-messaging-sw.js',
                { scope: '/' }
            );

            // Wait for service worker to be ready
            await navigator.serviceWorker.ready;

            // Send config to service worker
            if (registration.active) {
                registration.active.postMessage({
                    type: 'FIREBASE_CONFIG',
                    config: window.FIREBASE_CONFIG
                });
            }

            const messaging = firebase.messaging();
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

    window.initFirebaseMessaging = initFirebaseMessaging;

    // Auto-init if conditions are met
    if ('serviceWorker' in navigator) {
        document.addEventListener('DOMContentLoaded', initFirebaseMessaging);
    }
})();
