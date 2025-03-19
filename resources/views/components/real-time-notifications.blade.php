<div id="notifications" class="position-fixed end-0 p-3" style="z-index: 1050; top: 50px;"></div>

@push('scripts')


    <!-- Use Latest Firebase SDK Version -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", async function () {
            try {
                // Ensure Firebase is available
                if (typeof firebase === "undefined") {
                    console.error("Firebase SDK is missing. Make sure you included Firebase scripts.");
                    return;
                }

                // Initialize Firebase
                if (!firebase.apps.length) {
                    firebase.initializeApp({
                        apiKey: "{{ config('firebase.api_key') }}",
                        authDomain: "{{ config('firebase.auth_domain') }}",
                        projectId: "{{ config('firebase.project_id') }}",
                        messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
                        appId: "{{ config('firebase.app_id') }}",
                    });
                }

                const messaging = firebase.messaging();
                console.log("Firebase Messaging Initialized Successfully");

                // Request Notification Permission
                const permission = await Notification.requestPermission();
                if (permission !== "granted") {
                    console.warn("Notification permission denied.");
                    return;
                }

                // Foreground Notification Handler (Uses Exact Payload Format)
                messaging.onMessage((payload) => {
                    console.log("Foreground Notification Received:", payload);

                    const notificationBadge = document.getElementById('notification-badge');
                    const notificationBadge2 = document.getElementById('notification-badge2');
                        notificationBadge.style.display = 'block';
                        notificationBadge2.style.display = 'block';

                    // Extract Data Directly from Payload (No Format Changes)
                    const title = payload.notification?.title || payload.data?.heading;
                    const message = payload.notification?.body || payload.data?.message;
                    const link = payload.data?.link || "#";
                    let image = payload.notification?.image || payload.data?.image;

                    // Ensure Image URL is absolute
                    if (image && !image.startsWith("http")) {
                        image = `${window.location.origin}/${image}`;
                    }

                    // Select Notification Container
                    let notifications = document.getElementById("notifications");
                    if (!notifications) {
                        console.error("Element #notifications is missing in the HTML.");
                        return;
                    }

                    // Create Toast Notification
                    let newNotification = document.createElement("div");
                    newNotification.className = "toast show align-items-center border-0 shadow-sm";
                    newNotification.setAttribute("role", "alert");
                    newNotification.setAttribute("aria-live", "assertive");
                    newNotification.setAttribute("aria-atomic", "true");

                    // Toast HTML (Uses Data from Payload Directly)
                    newNotification.innerHTML = `
                <div class="toast show align-items-center text-bg-light border-0 shadow-sm position-relative my-1">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="toast" aria-label="Close"></button>

                    <!-- Toast Body -->
                    <div class="toast-body">
                        <a href="${window.location.origin + '/' + link}" class="text-decoration-none text-dark">
                            <div class="d-flex align-items-center">
                                <img src="${image || '{{ asset('img/placeholder-img.jfif') }}'}" alt="Notification" class="rounded me-3" width="50" height="50">
                                <div>
                                    <strong>${title}</strong>
                                    <p class="mb-0">${message}</p>
                                    <small class="text-muted">${new Date().toLocaleString()}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            `;

                    // Add Notification to Container
                    notifications.prepend(newNotification);

                    // Automatically Remove Notification After 5 Seconds
                    setTimeout(() => {
                        newNotification.classList.remove("show");
                        setTimeout(() => newNotification.remove(), 500);
                    }, 10000);
                });

            } catch (error) {
                console.error("Error initializing Firebase Messaging:", error);
            }
        });



    </script>





@endpush


<!--
 <script>
    let pusher = new Pusher(@json(env('PUSHER_APP_KEY')), {
        cluster: @json(env('PUSHER_APP_CLUSTER', 'ap2')),
        encrypted: true
    });

    let userId = @json(auth()->user()->id ?? null);
    let channel = pusher.subscribe(`private-App.Models.User.${userId}`);

    channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
        let notifications = document.getElementById('notifications');

        let newNotification = document.createElement('div');
        newNotification.className = "toast show align-items-center  border-0 shadow-sm";
        newNotification.setAttribute("role", "alert");
        newNotification.setAttribute("aria-live", "assertive");
        newNotification.setAttribute("aria-atomic", "true");

        newNotification.innerHTML = `
                <div class="toast show align-items-center text-bg-light border-0 shadow-sm position-relative my-1">

                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="toast" aria-label="Close"></button>

                <div class="toast-body">
                    <a href="${window.location.origin + '/' + data.link}"  class="text-decoration-none text-dark">
                        <div class="d-flex align-items-center">
                            <img src="${data.image ? '{{ asset('/') }}' + data.image : '{{ asset('img/placeholder-img.jfif') }}'}" alt="Notification" class="rounded me-3" width="50" height="50">
                            <div>
                                <strong>${data.heading}</strong>
                                <p class="mb-0">${data.message}</p>
                                <small class="text-muted">${new Date().toLocaleString()}</small>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="toast-progress position-absolute"></div>
            </div>

            `;

        notifications.prepend(newNotification);

        // Auto-hide the notification after 5 seconds
        setTimeout(() => {
            newNotification.classList.remove("show");
            setTimeout(() => newNotification.remove(), 500);
        }, 5000);
    });

    Pusher.logToConsole = false;
</script>
-->
