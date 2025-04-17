function showNotification(title, message, link, image) {
    const notifications = document.getElementById("notifications");
    if (!notifications) return;

    // Ensure image URL is absolute
    if (image && !image.startsWith("http")) {
        image = `${window.location.origin}/${image}`;
    }

    const newNotification = document.createElement("div");
    newNotification.className = "toast show align-items-center border-0 shadow-sm";
    newNotification.setAttribute("role", "alert");
    newNotification.setAttribute("aria-live", "assertive");
    newNotification.setAttribute("aria-atomic", "true");

    newNotification.innerHTML = `
        <div class="toast show align-items-center text-bg-light border-0 shadow-sm position-relative my-1">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="toast" aria-label="Close"></button>
            <div class="toast-body">
                <a href="${window.location.origin + '/' + link}" class="text-decoration-none text-dark">
                    <div class="d-flex align-items-center">
                        <img src="${image || '/img/placeholder-img.jfif'}" alt="Notification" class="rounded me-3" width="50" height="50">
                        <div>
                            <strong>${title}</strong>
                            <p class="mb-0">${message}</p>
                            <small class="text-muted">${new Date().toLocaleString()}</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="toast-progress position-absolute bottom-0 start-0 bg-primary"
                 style="height: 3px; width: 100%; transition: width 10s linear;"></div>
        </div>
    `;

    notifications.prepend(newNotification);

    ['notification-badge', 'notification-badge2'].forEach(id => {
        const badge = document.getElementById(id);
        if (badge) badge.style.display = 'block';
    });

    setTimeout(() => {
        const progressBar = newNotification.querySelector('.toast-progress');
        if (progressBar) progressBar.style.width = '0%';
    }, 10);

    setTimeout(() => {
        newNotification.classList.remove("show");
        setTimeout(() => newNotification.remove(), 500);
    }, 10000);
}

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const firebaseConfig = window.FIREBASE_CONFIG;
        const app = firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        const permission = await Notification.requestPermission();
        if (permission === "granted") {
            messaging.onMessage((payload) => {
                console.log("🔔 Firebase Notification Payload:", payload);

                const parsedLink = JSON.parse(payload.data?.link || "{}");
                showNotification(
                    payload.notification?.title || payload.data?.heading || "New Notification",
                    payload.notification?.body || payload.data?.message || "",
                    parsedLink?.web || "#",
                    payload.notification?.image || payload.data?.image
                );
            });
        }
    } catch (error) {
        console.error("Firebase error:", error);
    }

    // Initialize Pusher
    if (window.PUSHER_CONFIG.appKey) {
        const pusher = new Pusher(window.PUSHER_CONFIG.appKey, {
            cluster: window.PUSHER_CONFIG.appCluster || 'ap2',
            encrypted: true
        });

        const userId = document.querySelector('meta[name="user-id"]')?.content;
        if (userId) {
            const channel = pusher.subscribe(`private-App.Models.User.${userId}`);
            channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (data) => {
                showNotification(
                    data.heading,
                    data.message,
                    data.link,
                    data.image ? `${window.location.origin}/${data.image}` : null
                );
            });
        }
    }
});
