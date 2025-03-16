
<nav class="navbar navbar-expand fixed-top" id="navbar_top">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        <!-- Sidenav toggler button -->
        <span class="inline-span" id="sidenav_toggler" onclick="openNav()">
            <i class='bx bx-menu-alt-left'></i>
        </span>

        <div id="top-nav-logo">
            <a href="{{url('admin_dashboard')}}" class="application-box">
                <svg width="50" height="50" class="application-logo"  viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_260_1679)">
                        <path d="M0.0961914 37.4784C0.226804 37.3115 0.39438 37.2044 0.482573 37.0515C2.24984 33.9872 4.00564 30.9164 5.76858 27.8496C7.58833 24.6841 9.40968 21.5194 11.237 18.3583C12.6019 15.9972 13.9813 13.6444 15.3466 11.2836C16.0138 10.1299 16.6629 8.96567 17.3228 7.80768C17.382 7.70379 17.4603 7.61076 17.5663 7.46094C23.4032 17.5883 29.2087 27.6614 35.064 37.8208C34.652 37.8208 34.3109 37.8208 33.8929 37.7934C32.0929 37.7585 30.3698 37.7506 28.6466 37.7461C28.6035 37.746 28.5604 37.784 28.5173 37.8043C28.1134 37.8996 27.8632 37.7745 27.6466 37.3848C26.7833 35.8316 25.8768 34.3023 24.9886 32.7628C23.3408 29.9069 21.6937 27.0506 20.048 24.1934C19.272 22.846 18.5002 21.4961 17.7255 20.148C17.6791 20.0672 17.6232 19.9919 17.5407 19.8671C16.9981 20.7974 16.4742 21.69 15.9558 22.5858C14.4135 25.251 12.8702 27.9157 11.3334 30.5842C10.0508 32.8112 8.7777 35.0437 7.49769 37.2722C7.39572 37.4497 7.27272 37.6151 7.09454 37.7737C5.16246 37.7529 3.29535 37.744 1.42822 37.7395C1.37001 37.7394 1.3117 37.7844 1.25344 37.8084C0.897236 37.813 0.541036 37.8177 0.140513 37.8232C0.0961914 37.7216 0.0961914 37.6192 0.0961914 37.4784Z" id="fill2"/>
                        <path d="M48.0959 37.7856C45.8883 37.8243 43.6807 37.816 41.4733 37.8336C41.1326 37.8363 40.9239 37.7533 40.7538 37.4487C40.0115 36.1197 39.2528 34.7998 38.4923 33.481C37.685 32.0812 36.8648 30.6889 36.0584 29.2887C35.3924 28.1324 34.7442 26.9659 34.0777 25.81C33.1784 24.2504 32.2676 22.6975 31.3652 21.1398C31.1269 20.7284 30.9013 20.3097 30.6534 19.8647C29.9341 21.0995 29.2253 22.3163 28.4828 23.5909C27.79 22.3966 27.1443 21.2817 26.4969 20.1678C26.0686 19.431 25.6548 18.685 25.2013 17.964C24.9468 17.5594 24.9417 17.2122 25.1883 16.8024C25.9728 15.4985 26.7389 14.1835 27.5003 12.866C28.5336 11.078 29.556 9.28365 30.6333 7.40454C30.8482 7.77531 31.0289 8.08745 31.21 8.39927C32.2866 10.2521 33.368 12.1021 34.4384 13.9584C35.4699 15.747 36.4856 17.5447 37.5163 19.3338C38.6551 21.3103 39.8073 23.279 40.9457 25.2557C41.8373 26.804 42.7103 28.363 43.6032 29.9106C44.6276 31.6859 45.6633 33.4548 46.6973 35.2246C47.1423 35.9863 47.5953 36.7433 48.0702 37.4712C48.0959 37.5424 48.0959 37.6448 48.0959 37.7856Z" id="fill3"/>
                        <path d="M1.29736 37.814C1.31131 37.7844 1.36962 37.7394 1.42783 37.7395C3.29496 37.744 5.16207 37.7529 7.0628 37.7806C5.17816 37.8066 3.25992 37.8131 1.29736 37.814Z"  id="fill4"/>
                        <path d="M28.5615 37.8108C28.5604 37.7841 28.6035 37.746 28.6465 37.7461C30.3697 37.7506 32.0929 37.7586 33.8489 37.7871C32.1231 37.8111 30.3645 37.8141 28.5615 37.8108Z" id="fill5"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_260_1679">
                            <rect width="50" height="50" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>

                <div class="application-text">
                    <span class="application-logo-text">HMS</span>
                    <span class="application-tagline">Elevate Your Living</span>
                </div>
            </a>
        </div>

        @if(!empty($breadcrumbLinks))
            <div class="d-none d-md-block">
                <x-breadcrumb :links="$breadcrumbLinks" />
            </div>
        @endif


        <div style="display: {{ $searchVisible ? 'block' : 'none' }};">
            <!-- <div class="position-relative mx-auto" id="top-nav-search" style="width: 90%;">
                <input type="text" name="search" class="form-control pe-5"
                    style="width: 100%; background-color: #F1F2F7; box-shadow: none; height: 30px;"
                    placeholder="Search" value="{{ request('search') }}">
                <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                    style="pointer-events: none;"></i>
            </div> -->
        </div>




        <!-- For Large Screen -->
        <ul class="navbar-nav navbar-lg text-center justify-content-between d-none d-md-flex ms-auto align-items-center">

            <label class="switch">
                <input type="checkbox" id="theme-toggle">
                <span class="slider round"></span>
            </label>

            <!-- Profile Dropdown -->
            <li class="nav-item dropdown  d-flex align-items-center" style="padding-left: 1rem;">
                <a class="nav-link d-flex align-items-center" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <!-- Profile Image -->
                    <img src="{{ Auth::user() && Auth::user()->picture ? asset(Auth::user()->picture) : asset('img/avatar.png') }}"
                         class="rounded-circle me-2"
                         alt="User Image"
                         style="width: 2.5rem; height: 2.5rem;">

                    <!-- User Info -->
                    <div class="d-flex flex-column text-start user-info">
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                        <small class="">{{ Auth::user()->email }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end user-menu" aria-labelledby="userMenu">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile') }}">
                            <i class="bx bxs-user me-2"></i> Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bx bxs-cog me-2"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            <!-- Alerts Dropdown -->
            <li class="nav-item dropdown no-arrow mx-2 px-2">
                <a class="nav-link dropdown-toggle dropdown-toggle-no-arrow position-relative" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg width="20" height="25" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.4 17.2222C5.51684 17.2178 4.80073 16.5053 4.792 15.6222H7.992C7.99369 15.8361 7.9529 16.0482 7.872 16.2462C7.66212 16.7278 7.23345 17.079 6.72 17.1902H6.716H6.704H6.6896H6.6824C6.58945 17.2095 6.49492 17.2202 6.4 17.2222ZM12.8 14.8222H0V13.2222L1.6 12.4222V8.02217C1.55785 6.89346 1.81275 5.77347 2.3392 4.77417C2.86323 3.84738 3.75896 3.18927 4.8 2.96617V1.22217H8V2.96617C10.0632 3.45737 11.2 5.25257 11.2 8.02217V12.4222L12.8 13.2222V14.8222Z" fill="#B0C3CC"/>
                        <circle cx="11" cy="3" r="3" fill="#EC5252"/>
                    </svg>
                </a>
                <div class="dropdown-menu mt-3 dropdown-menu-end shadow animated--grow-in notification-menu" aria-labelledby="alertsDropdown">
                    <div class="notification-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 px-2 py-2 fw-bold">
                            <svg width="20" height="25" class="mx-2" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.4 17.2222C5.51684 17.2178 4.80073 16.5053 4.792 15.6222H7.992C7.99369 15.8361 7.9529 16.0482 7.872 16.2462C7.66212 16.7278 7.23345 17.079 6.72 17.1902H6.716H6.704H6.6896H6.6824C6.58945 17.2095 6.49492 17.2202 6.4 17.2222ZM12.8 14.8222H0V13.2222L1.6 12.4222V8.02217C1.55785 6.89346 1.81275 5.77347 2.3392 4.77417C2.86323 3.84738 3.75896 3.18927 4.8 2.96617V1.22217H8V2.96617C10.0632 3.45737 11.2 5.25257 11.2 8.02217V12.4222L12.8 13.2222V14.8222Z" fill="#B0C3CC"/>
                                <circle cx="11" cy="3" r="3" fill="#EC5252"/>
                            </svg>
                            Notifications
                        </h5>
                        <button class="btn-close px-3" aria-label="Close"></button>
                    </div>

                    <div class="notification-list  px-1"  id="notificationList">
                        <p class="text-muted text-center small" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">No new notifications</p>
                    </div>


                    <div class="notification-footer d-flex justify-content-between py-3 px-3">
                        <button id="markAllAsRead" class="btn btn-primary btn rounded-3">Mark All as Read</button>
                        <button class="btn btn-outline-secondary btn rounded-3">Close</button>
                    </div>
                </div>
            </li>

        </ul>


        <!-- For small Screen -->
        <ul class="navbar-nav navbar-sm text-center d-md-none ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <!-- Profile Image -->
                    <img src="{{ Auth::user() && Auth::user()->picture ? asset(Auth::user()->picture) : asset('img/avatar.png') }}"
                         class="rounded-circle me-2"
                         alt="User Image"
                         style="width: 2.5rem; height: 2.5rem;">

                    <!-- User Info -->
                    <div class="d-flex flex-column text-start user-info">
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                        <small class="">{{ Auth::user()->email }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end user-menu" aria-labelledby="userMenu">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile') }}">
                            <i class="bx bxs-user me-2"></i> Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bx bxs-cog me-2"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<script>
    document.querySelector('#sidenav_toggler').addEventListener('mouseover', function () {
        this.querySelector('i').classList.replace('bx-menu-alt-left', 'bx-menu');
    });

    document.querySelector('#sidenav_toggler').addEventListener('mouseout', function () {
        this.querySelector('i').classList.replace('bx-menu', 'bx-menu-alt-left');
    });

</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        function fetchNotifications() {
            console.log("Notification Function is calling");

            fetch("{{ route('notifications') }}", {
                method: "GET"
            })
                .then(response => response.json())
                .then(data => {

                    const notificationList = document.getElementById("notificationList");
                    notificationList.innerHTML = "";

                    if (data.notifications.length > 0) {
                        data.notifications.forEach(notification => {
                            const timeAgo = timeSince(new Date(notification.data.created_at));

                            notificationList.innerHTML += `
                           <a class="d-flex align-items-center text-decoration-none p-2 border-bottom" href="${notification.data.link}">
                                <img src="${notification.data.image ? '{{ asset('/') }}' + notification.data.image : '{{ asset('img/placeholder-img.jfif') }}'}" class="rounded-circle me-2 notification-item-img" style="width: 45px; height: 45px;" alt="Notification Image">
                                <div class="d-flex flex-column notification-item-div">
                                    <span class="${notification.read_at ? '' : 'fw-bold'}  text-dark small notification-item-heading">${notification.data.heading}</span>
                                    <span class="text-muted small text-wrap notification-item-message" style="font-size: 12px;">${notification.data.message}</span>
                                    <span class="text-muted small notification-item-time" style="font-size: 10px;">${timeAgo}</span>
                                </div>
                            </a>

                        `;
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

        // Fetch notifications on dropdown click
        document.getElementById("alertsDropdown").addEventListener("click", fetchNotifications);
    });
</script>

 <script>
     document.getElementById("markAllAsRead").addEventListener("click", function (event) {
         event.preventDefault();
         event.stopPropagation();

         fetch("{{ route('notifications.mark-all-as-read') }}", {
             method: "POST",
             headers: {
                 "X-CSRF-TOKEN": "{{ csrf_token() }}",
                 "Content-Type": "application/json"
             }
         })
             .then(response => {
                 if (!response.ok) {
                     throw new Error("Failed to mark notifications as read");
                 }
                 return response.json();
             })
             .then(() => {

                 setTimeout(() => {
                     let notifications = document.querySelectorAll(".notification-item-div");
                     if (notifications.length === 0) {
                         console.log("Notifications list is empty.");
                         return;
                     }
                     notifications.forEach(notification => {
                         let heading = notification.querySelector(".notification-item-heading");
                         if (heading) {
                             heading.classList.remove("fw-bold");
                         }
                     });
                 }, 0);
             })
             .catch(error => console.error("Error:", error));
     });
 </script>


