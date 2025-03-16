<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
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
            newNotification.className = "toast show align-items-center text-bg-light border-0 shadow-sm";
            newNotification.setAttribute("role", "alert");
            newNotification.setAttribute("aria-live", "assertive");
            newNotification.setAttribute("aria-atomic", "true");

            newNotification.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <a href="${data.link}" target="_blank" class="text-decoration-none text-dark">
                            <div class="d-flex align-items-center">
                                <img src="${data.image}" alt="Notification" class="rounded me-3" width="50" height="50">
                                <div>
                                    <strong>${data.heading}</strong>
                                    <p class="mb-0">${data.message}</p>
                                    <small class="text-muted">${new Date().toLocaleString()}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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
</head>
<body class="bg-light">

    <!-- Notifications Container (Fixed to Top Right) -->
    <div id="notifications" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

</body>
</html>
