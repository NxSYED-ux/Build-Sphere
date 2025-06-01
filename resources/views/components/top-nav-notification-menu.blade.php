<li class="nav-item dropdown no-arrow mx-2 px-2">
    <a class="nav-link dropdown-toggle dropdown-toggle-no-arrow position-relative" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <svg width="20" height="25" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.4 17.2222C5.51684 17.2178 4.80073 16.5053 4.792 15.6222H7.992C7.99369 15.8361 7.9529 16.0482 7.872 16.2462C7.66212 16.7278 7.23345 17.079 6.72 17.1902H6.716H6.704H6.6896H6.6824C6.58945 17.2095 6.49492 17.2202 6.4 17.2222ZM12.8 14.8222H0V13.2222L1.6 12.4222V8.02217C1.55785 6.89346 1.81275 5.77347 2.3392 4.77417C2.86323 3.84738 3.75896 3.18927 4.8 2.96617V1.22217H8V2.96617C10.0632 3.45737 11.2 5.25257 11.2 8.02217V12.4222L12.8 13.2222V14.8222Z" fill="#B0C3CC"/>
            <circle id="notification-badge" cx="11" cy="3" r="3" fill="#EC5252" style="display: none;"/>
        </svg>
    </a>
    <div class="dropdown-menu mt-3 dropdown-menu-end shadow animated--grow-in notification-menu" aria-labelledby="alertsDropdown">
        <div class="notification-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 px-2 py-2 fw-bold">
                <svg width="20" height="25" class="mx-2" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.4 17.2222C5.51684 17.2178 4.80073 16.5053 4.792 15.6222H7.992C7.99369 15.8361 7.9529 16.0482 7.872 16.2462C7.66212 16.7278 7.23345 17.079 6.72 17.1902H6.716H6.704H6.6896H6.6824C6.58945 17.2095 6.49492 17.2202 6.4 17.2222ZM12.8 14.8222H0V13.2222L1.6 12.4222V8.02217C1.55785 6.89346 1.81275 5.77347 2.3392 4.77417C2.86323 3.84738 3.75896 3.18927 4.8 2.96617V1.22217H8V2.96617C10.0632 3.45737 11.2 5.25257 11.2 8.02217V12.4222L12.8 13.2222V14.8222Z" fill="#B0C3CC"/>
                    <circle id="notification-badge2" cx="11" cy="3" r="3" fill="#EC5252" style="display: none;"/>
                </svg>
                Notifications
            </h5>
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark p-0 mx-2" type="button" id="notificationActionsBtn">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>

        <!-- Selection Actions Panel (hidden by default) -->
        <div class="notification-actions px-3 py-2 border-bottom bg-light" id="notificationActions" style="display: none;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                    <label class="form-check-label small" for="selectAllCheckbox">Select All</label>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-danger me-2" id="deleteSelectedBtn" disabled>
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                    <button class="btn btn-sm btn-outline-primary" id="markReadSelectedBtn" disabled>
                        <i class="fas fa-envelope-open me-1"></i> Mark as Read
                    </button>
                </div>
            </div>
        </div>

        <div class="notification-list px-1" id="notificationList">
            <p class="text-muted text-center small" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">No new notifications</p>
        </div>

        <div class="notification-footer d-flex justify-content-between py-3 px-3">
            <button id="markAllAsRead" class="btn btn-primary btn rounded-3">Mark All as Read</button>
            <button class="btn btn-outline-secondary btn rounded-3">Close</button>
        </div>
    </div>
</li>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let selectedNotifications = [];
            let selectionMode = false;

            // Toggle selection mode when three-dot button is clicked
            document.getElementById('notificationActionsBtn').addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSelectionMode();
            });

            function toggleSelectionMode() {
                selectionMode = !selectionMode;
                const actionsPanel = document.getElementById('notificationActions');
                actionsPanel.style.display = selectionMode ? 'block' : 'none';

                // Show/hide checkboxes
                document.querySelectorAll('.notification-checkbox-container').forEach(el => {
                    el.style.display = selectionMode ? 'block' : 'none';
                });

                // Clear selection when exiting selection mode
                if (!selectionMode) {
                    selectedNotifications = [];
                    document.getElementById('selectAllCheckbox').checked = false;
                    updateActionButtons();
                }
            }

            // Update action buttons based on selection count
            function updateActionButtons() {
                const deleteBtn = document.getElementById('deleteSelectedBtn');
                const markReadBtn = document.getElementById('markReadSelectedBtn');

                if (selectedNotifications.length > 0) {
                    deleteBtn.disabled = false;
                    markReadBtn.disabled = false;
                } else {
                    deleteBtn.disabled = true;
                    markReadBtn.disabled = true;
                }
            }

            // Select/Deselect all checkbox
            document.getElementById('selectAllCheckbox').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.notification-checkbox');
                selectedNotifications = [];

                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    if (this.checked) {
                        selectedNotifications.push(checkbox.value);
                    }
                });
                updateActionButtons();
            });

            // Individual checkbox change handler
            function handleCheckboxChange() {
                const notificationId = this.value;
                if (this.checked) {
                    if (!selectedNotifications.includes(notificationId)) {
                        selectedNotifications.push(notificationId);
                    }
                } else {
                    selectedNotifications = selectedNotifications.filter(id => id !== notificationId);
                    document.getElementById('selectAllCheckbox').checked = false;
                }
                updateActionButtons();
            }

            // Delete selected notifications
            document.getElementById('deleteSelectedBtn').addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default behavior
                e.stopPropagation(); // Stop event bubbling

                if (selectedNotifications.length === 0) return;

                // Keep notification menu open by preventing Bootstrap's default close behavior
                const dropdown = document.getElementById('alertsDropdown');
                const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown);

                // Show centered confirmation SweetAlert
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete them!',
                    position: 'center', // Center the alert
                    backdrop: true, // Show backdrop
                    allowOutsideClick: false // Prevent closing by clicking outside
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('notifications.remove-batch') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ notification_ids: selectedNotifications })
                        })
                            .then(response => {
                                if (!response.ok) throw new Error("Failed to delete notifications");
                                return response.json();
                            })
                            .then(() => {
                                // Remove deleted notifications from UI
                                selectedNotifications.forEach(id => {
                                    const element = document.querySelector(`.notification-item[data-id="${id}"]`);
                                    if (element) element.remove();
                                });

                                // Reset selection
                                selectedNotifications = [];
                                document.getElementById('selectAllCheckbox').checked = false;
                                updateActionButtons();

                                // Check if all notifications are deleted
                                if (document.querySelectorAll('.notification-item').length === 0) {
                                    document.getElementById('notificationList').innerHTML =
                                        '<p class="text-muted text-center small" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">No new notifications</p>';
                                }

                                // Update unread count
                                updateUnreadCount();

                                // Re-open the notification menu if it was closed
                                if (!dropdown.classList.contains('show')) {
                                    dropdownInstance.show();
                                }

                                // Show success SweetAlert
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Your notifications have been deleted.',
                                    icon: 'success',
                                    position: 'center',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                // Re-open the notification menu if it was closed
                                if (!dropdown.classList.contains('show')) {
                                    dropdownInstance.show();
                                }

                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to delete notifications.',
                                    icon: 'error',
                                    position: 'center'
                                });
                            });
                    } else {
                        // Re-open the notification menu if it was closed
                        if (!dropdown.classList.contains('show')) {
                            dropdownInstance.show();
                        }
                    }
                });
            });

            // Mark selected as read
            {{--document.getElementById('markReadSelectedBtn').addEventListener('click', function() {--}}
            {{--    if (selectedNotifications.length === 0) return;--}}

            {{--    fetch("{{ route('notifications.mark-selected-as-read') }}", {--}}
            {{--        method: "POST",--}}
            {{--        headers: {--}}
            {{--            "X-CSRF-TOKEN": "{{ csrf_token() }}",--}}
            {{--            "Content-Type": "application/json"--}}
            {{--        },--}}
            {{--        body: JSON.stringify({ notification_ids: selectedNotifications })--}}
            {{--    })--}}
            {{--        .then(response => {--}}
            {{--            if (!response.ok) throw new Error("Failed to mark notifications as read");--}}
            {{--            return response.json();--}}
            {{--        })--}}
            {{--        .then(() => {--}}
            {{--            selectedNotifications.forEach(id => {--}}
            {{--                const element = document.querySelector(`.notification-item[data-id="${id}"]`);--}}
            {{--                if (element) {--}}
            {{--                    element.classList.remove('unread');--}}
            {{--                    const heading = element.querySelector('.notification-item-heading');--}}
            {{--                    if (heading) heading.classList.remove('fw-bold');--}}
            {{--                }--}}
            {{--            });--}}

            {{--            // Reset selection--}}
            {{--            selectedNotifications = [];--}}
            {{--            document.getElementById('selectAllCheckbox').checked = false;--}}
            {{--            updateActionButtons();--}}

            {{--            // Update badge count--}}
            {{--            updateUnreadCount();--}}
            {{--        })--}}
            {{--        .catch(error => console.error("Error:", error));--}}
            {{--});--}}

            function fetchNotifications() {
                fetch("{{ route('notifications') }}", {
                    method: "GET",
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const notificationList = document.getElementById("notificationList");
                        notificationList.innerHTML = "";

                        if (data.notifications.length > 0) {
                            data.notifications.forEach(notification => {
                                const timeAgo = timeSince(new Date(notification.data.created_at));

                                notificationList.innerHTML += `
                                <div class="d-flex align-items-center p-2 border-bottom notification-item ${notification.read_at ? '' : 'unread'}"
                                    data-id="${notification.id}">
                                    <div class="form-check me-2 notification-checkbox-container" style="display: none;">
                            		<input class="form-check-input notification-checkbox" type="checkbox"
                                	    value="${notification.id}" id="notif-${notification.id}">
                        	    </div>
                                    <a class="d-flex align-items-center text-decoration-none flex-grow-1 notification-link"
                                        ${notification.data.link.web ? `href="${notification.read_at ? window.location.origin + '/' + notification.data.link.web : 'javascript:void(0);'}"` : ''}
                                        onclick="${notification.read_at ? '' : `markNotificationAsRead('${notification.id}', '${window.location.origin}/${notification.data.link.web}')`}">
                                        <img src="${notification.data.image ? '{{ asset('/') }}' + notification.data.image : '{{ asset('img/placeholder-img.jfif') }}'}"
                                            class="rounded-circle me-2 notification-item-img"
                                            style="width: 45px; height: 45px;"
                                            alt="Notification Image">
                                        <div class="d-flex flex-column notification-item-div">
                                            <span class="${notification.read_at ? '' : 'fw-bold'} text small notification-item-heading">${notification.data.heading}</span>
                                            <span class="text-muted small text-wrap notification-item-message" style="font-size: 12px;">${notification.data.message}</span>
                                            <span class="text-muted small notification-item-time" style="font-size: 10px;">${timeAgo}</span>
                                        </div>
                                    </a>
                                </div>
                            `;
                            });

                            // Add event listeners to checkboxes after they're created
                            document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
                                checkbox.addEventListener('change', handleCheckboxChange);
                            });
                        } else {
                            notificationList.innerHTML = '<p class="text-muted text-center small" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">No new notifications</p>';
                        }
                    })
                    .catch(error => console.error("Error fetching notifications:", error));
            }

            function timeSince(date) {
                const seconds = Math.floor((new Date() - date) / 1000);
                const intervals = {
                    year: 31536000, month: 2592000, day: 86400,
                    hour: 3600, minute: 60, second: 1
                };
                for (let [key, value] of Object.entries(intervals)) {
                    let count = Math.floor(seconds / value);
                    if (count > 0) {
                        return count + " " + key + (count > 1 ? "s" : "") + "";
                    }
                }
                return "Just now";
            }



            function updateUnreadCount() {
                fetch("{{ route('notifications.unread.count') }}", {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const notificationBadge = document.getElementById('notification-badge');
                        const notificationBadge2 = document.getElementById('notification-badge2');
                        notificationBadge.style.display = data.count > 0 ? 'block' : 'none';
                        notificationBadge2.style.display = data.count > 0 ? 'block' : 'none';
                    })
                    .catch(error => console.error("Error:", error));
            }

            document.getElementById("alertsDropdown").addEventListener("click", fetchNotifications);

            document.getElementById("markAllAsRead").addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();

                fetch("{{ route('notifications.mark-all-as-read') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error("Failed to mark notifications as read");
                        return response.json();
                    })
                    .then(() => {
                        document.querySelectorAll(".notification-item").forEach(item => {
                            item.classList.remove('unread');
                            const heading = item.querySelector(".notification-item-heading");
                            if (heading) heading.classList.remove("fw-bold");
                        });
                        updateUnreadCount();
                    })
                    .catch(error => console.error("Error:", error));
            });
        });

        function markNotificationAsRead(notificationId, redirectUrl) {
            if (!notificationId || !redirectUrl) {
                return;
            }

            fetch("{{ route('notifications.mark-single-as-read') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ notification_id: notificationId })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Failed to mark notification as read");
                    }
                    return response.json();
                })
                .then(() => {
                    let notificationElement = document.querySelector(`[data-id="${notificationId}"]`);
                    if (notificationElement) {
                        let heading = notificationElement.querySelector(".notification-item-heading");
                        if (heading) {
                            heading.classList.remove("fw-bold");
                            notificationElement.classList.remove('unread');
                        }
                    }

                    setTimeout(() => {
                        console.log("Redirecting to:", redirectUrl);
                        window.location.href = redirectUrl;
                    }, 300);
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error marking notification as read. Please try again.");
                });
        }
    </script>
@endpush
