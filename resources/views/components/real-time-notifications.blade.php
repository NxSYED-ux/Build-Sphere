<div id="notifications" class="position-fixed end-0 p-3" style="z-index: 1050; top: 50px;"></div>

@push('scripts')

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
                <!-- Close Button -->
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="toast" aria-label="Close"></button>

                <!-- Toast Body -->
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

                <!-- Progress Bar -->
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

@endpush
