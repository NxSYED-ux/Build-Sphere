@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4">🔔 Real-Time Notifications</h2>

        <div id="notifications" class="card shadow-sm p-3">
            <h5 class="text-muted">No new notifications</h5>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let userId = 1;

            window.Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    console.log('New notification:', notification);

                    let notificationsContainer = document.getElementById('notifications');

                    if (notificationsContainer.innerHTML.includes("No new notifications")) {
                        notificationsContainer.innerHTML = "";
                    }

                    let newNotification = `
                        <div class="alert alert-info d-flex align-items-center gap-3 p-3 mb-2">
                            <img src="${notification.picture}" width="50" class="rounded-circle" alt="Notification">
                            <div>
                                <strong>${notification.heading}</strong>
                                <p>${notification.message}</p>
                                <a href="${notification.link}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    `;

                    notificationsContainer.innerHTML += newNotification;
                });
        });
    </script>
@endsection
