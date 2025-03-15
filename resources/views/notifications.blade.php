@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Real-Time Notifications Test</h2>
        <button id="sendNotification" class="btn btn-primary">Send Test Notification</button>
        <div id="notifications" class="mt-3"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let userId = @json(1);

            if (!userId) {
                showError("User not authenticated!");
                return;
            }

            try {
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('New notification:', notification);
                        displayNotification(notification);
                    })
                    .error(error => {
                        showError("Failed to subscribe to notifications.");
                        console.error("Echo subscription error:", error);
                    });
            } catch (err) {
                showError("WebSocket connection error." . err);
                console.error("Echo initialization error:", err);
            }

            document.getElementById('sendNotification').addEventListener('click', function() {
                fetch('/send-notification')
                    .then(response => response.json())
                    .then(data => console.log("Notification sent:", data))
                    .catch(error => showError("Failed to send notification."));
            });

            function displayNotification(notification) {
                let notificationsContainer = document.getElementById('notifications');
                let newNotification = `
                    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                        <strong>${notification.heading || "New Notification"}</strong>
                        <p>${notification.message || "You have a new notification."}</p>
                        ${notification.link ? `<a href="${notification.link}" target="_blank">View</a>` : ''}
                    </div>`;
                notificationsContainer.innerHTML += newNotification;
            }

            function showError(message) {
                let notificationsContainer = document.getElementById('notifications');
                let errorDiv = `<div style="color: red; font-weight: bold;">${message}</div>`;
                notificationsContainer.innerHTML += errorDiv;
            }
        });
    </script>
@endsection
