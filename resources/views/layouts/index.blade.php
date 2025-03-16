<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            let newNotification = document.createElement('a');
            newNotification.href = data.link;
            newNotification.target = "_blank";
            newNotification.className = "block bg-white border border-gray-300 rounded-lg p-4 shadow-md hover:shadow-lg transition-all duration-300 flex items-start space-x-4";

            newNotification.innerHTML = `
                <img src="${data.image}" alt="Notification" class="w-16 h-16 rounded-md shadow">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">${data.heading}</h2>
                    <p class="text-gray-600 text-sm">${data.message}</p>
                    <p class="text-xs text-gray-400 mt-1">${new Date().toLocaleString()}</p>
                </div>
            `;

            notifications.prepend(newNotification);
        });

        Pusher.logToConsole = false;
    </script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-lg">
    <h1 class="text-xl font-bold text-center text-gray-800">Live Notifications</h1>
    <p class="text-gray-600 text-center">New notifications will appear here.</p>
    <div id="notifications" class="mt-4 space-y-3"></div>
</div>
</body>
</html>
