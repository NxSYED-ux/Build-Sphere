@props([
    'profileRoute' => '#',
    'settingsRoute' => '#',
    'logoutRoute' => '#',
    'orgProfileRoute' => '',
])

<li class="nav-item dropdown" >
    <a class="nav-link d-flex align-items-center" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="{{ Auth::user() && Auth::user()->picture ? asset(Auth::user()->picture) : asset('img/avatar.png') }}"
             class="rounded-circle me-2"
             alt="User Image"
             style="width: 2.5rem; height: 2.5rem;">

        <div class="d-flex flex-column text-start user-info">
            <span class="fw-bold">{{ Auth::user()->name }}</span>
            <small class="">{{ Auth::user()->email }}</small>
        </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end user-menu" aria-labelledby="userMenu">
        <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ $profileRoute }}">
                <i class="bx bxs-user me-2"></i> Profile
            </a>
        </li>
        @if($orgProfileRoute)
            <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ $orgProfileRoute }}">
                    <i class="bx bxs-user-account me-2"></i> Org Profile
                </a>
            </li>
        @endif
{{--        <li>--}}
{{--            <a class="dropdown-item d-flex align-items-center" href="{{ $settingsRoute }}">--}}
{{--                <i class="bx bxs-cog me-2"></i> Settings--}}
{{--            </a>--}}
{{--        </li>--}}
        <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ $logoutRoute }}"
               onclick="event.preventDefault(); handleLogout();">
                <i class="fa fa-power-off me-2"></i> Logout
            </a>
            <form id="logout-form" action="{{ $logoutRoute }}" method="POST" class="d-none">
                @csrf
                <input type="hidden" name="fcmToken" id="fcm_token">
            </form>
        </li>

        <script>
            async function handleLogout() {
                const fcmToken = localStorage.getItem('newFcmToken');

                if (fcmToken) {
                    document.getElementById('fcm_token').value = fcmToken;

                    localStorage.removeItem('newFcmToken');
                }

                if ('serviceWorker' in navigator) {
                    const registration = await navigator.serviceWorker.getRegistration('/firebase-messaging-sw.js');
                    if (registration) {
                        await registration.unregister();
                        console.log('Firebase messaging service worker unregistered.');
                    }
                }

                document.getElementById('logout-form').submit();
            }
        </script>

    </ul>
</li>
