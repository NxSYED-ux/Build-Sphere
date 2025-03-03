
<style>
    .switch {
  position: relative;
  display: inline-block;
  width: 55px;
  height: 27px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider.round {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  border-radius: 34px;
  transition: .4s;
}

.slider.round:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  border-radius: 50%;
  transition: .4s;
}

input:checked + .slider.round {
  background-color: #2196F3;
}

input:checked + .slider.round:before {
  transform: translateX(26px);
}
</style>
<nav class="navbar navbar-expand fixed-top" id="navbar_top">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        <!-- Sidenav toggler button -->
        <span class="inline-span" id="sidenav_toggler" onclick="openNav()">
            <i class='bx bx-menu-alt-left'></i>
        </span>

        <a href="{{url('admin_dashboard')}}" id="top-nav-logo">
            <svg width="48" height="48" class="application-logo"  viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_260_1679)">
                    <path d="M0.0961914 37.4784C0.226804 37.3115 0.39438 37.2044 0.482573 37.0515C2.24984 33.9872 4.00564 30.9164 5.76858 27.8496C7.58833 24.6841 9.40968 21.5194 11.237 18.3583C12.6019 15.9972 13.9813 13.6444 15.3466 11.2836C16.0138 10.1299 16.6629 8.96567 17.3228 7.80768C17.382 7.70379 17.4603 7.61076 17.5663 7.46094C23.4032 17.5883 29.2087 27.6614 35.064 37.8208C34.652 37.8208 34.3109 37.8208 33.8929 37.7934C32.0929 37.7585 30.3698 37.7506 28.6466 37.7461C28.6035 37.746 28.5604 37.784 28.5173 37.8043C28.1134 37.8996 27.8632 37.7745 27.6466 37.3848C26.7833 35.8316 25.8768 34.3023 24.9886 32.7628C23.3408 29.9069 21.6937 27.0506 20.048 24.1934C19.272 22.846 18.5002 21.4961 17.7255 20.148C17.6791 20.0672 17.6232 19.9919 17.5407 19.8671C16.9981 20.7974 16.4742 21.69 15.9558 22.5858C14.4135 25.251 12.8702 27.9157 11.3334 30.5842C10.0508 32.8112 8.7777 35.0437 7.49769 37.2722C7.39572 37.4497 7.27272 37.6151 7.09454 37.7737C5.16246 37.7529 3.29535 37.744 1.42822 37.7395C1.37001 37.7394 1.3117 37.7844 1.25344 37.8084C0.897236 37.813 0.541036 37.8177 0.140513 37.8232C0.0961914 37.7216 0.0961914 37.6192 0.0961914 37.4784Z" id="fill2"/>
                    <path d="M48.0959 37.7856C45.8883 37.8243 43.6807 37.816 41.4733 37.8336C41.1326 37.8363 40.9239 37.7533 40.7538 37.4487C40.0115 36.1197 39.2528 34.7998 38.4923 33.481C37.685 32.0812 36.8648 30.6889 36.0584 29.2887C35.3924 28.1324 34.7442 26.9659 34.0777 25.81C33.1784 24.2504 32.2676 22.6975 31.3652 21.1398C31.1269 20.7284 30.9013 20.3097 30.6534 19.8647C29.9341 21.0995 29.2253 22.3163 28.4828 23.5909C27.79 22.3966 27.1443 21.2817 26.4969 20.1678C26.0686 19.431 25.6548 18.685 25.2013 17.964C24.9468 17.5594 24.9417 17.2122 25.1883 16.8024C25.9728 15.4985 26.7389 14.1835 27.5003 12.866C28.5336 11.078 29.556 9.28365 30.6333 7.40454C30.8482 7.77531 31.0289 8.08745 31.21 8.39927C32.2866 10.2521 33.368 12.1021 34.4384 13.9584C35.4699 15.747 36.4856 17.5447 37.5163 19.3338C38.6551 21.3103 39.8073 23.279 40.9457 25.2557C41.8373 26.804 42.7103 28.363 43.6032 29.9106C44.6276 31.6859 45.6633 33.4548 46.6973 35.2246C47.1423 35.9863 47.5953 36.7433 48.0702 37.4712C48.0959 37.5424 48.0959 37.6448 48.0959 37.7856Z" id="fill3"/>
                    <path d="M1.29736 37.814C1.31131 37.7844 1.36962 37.7394 1.42783 37.7395C3.29496 37.744 5.16207 37.7529 7.0628 37.7806C5.17816 37.8066 3.25992 37.8131 1.29736 37.814Z"  id="fill4"/>
                    <path d="M28.5615 37.8108C28.5604 37.7841 28.6035 37.746 28.6465 37.7461C30.3697 37.7506 32.0929 37.7586 33.8489 37.7871C32.1231 37.8111 30.3645 37.8141 28.5615 37.8108Z" id="fill5"/>
                </g>
                <defs>
                    <clipPath id="clip0_260_1679">
                        <rect width="48" height="48" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
            <span class="application-logo-text">HEIGHTS</span>
        </a>

        @if(!empty($breadcrumbLinks))
        <x-breadcrumb :links="$breadcrumbLinks" />
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
            <li class="nav-item dropdown d-flex align-items-center" style="padding-left: 1rem;">
                <a class="nav-link d-flex align-items-center" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <!-- Profile Image -->
                    <img src="{{ Auth::user() && Auth::user()->picture ? asset(Auth::user()->picture) : asset('img/avatar.png') }}"
                        class="rounded-circle me-2"
                        alt="User Image"
                        style="width: 2.5rem; height: auto;">

                    <!-- User Info -->
                    <div class="d-flex flex-column text-start user-info">
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                        <small class="">{{ Auth::user()->email }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-0" style="z-index: 1050;" aria-labelledby="userMenu">
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
                <a class="nav-link dropdown-toggle position-relative" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bell fa-fw notification-icon"></i>
                    <span class="notification-badge"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in notification-menu" aria-labelledby="alertsDropdown">
                    <div class="notification-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 px-2"><i class="fa fa-bell fa-fw notification-icon"></i> Notifications</h6>
                        <button class="btn-close px-3" aria-label="Close"></button>
                    </div>
                    <div class="notification-list">
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <img src="{{ asset('img/buildings/building6.jpg') }}" style="width: 50px; height: 50px;" alt="Notification Image" class="rounded-circle me-3">
                            <div>
                                <strong>Notification 1</strong>
                                <p class="mb-0 text-muted text-wrap small">Building status successfully changed to Approved.</p>
                            </div>
                            <span class="text-muted small ms-auto">2m ago</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <img src="{{ asset('img/buildings/building6.jpg') }}" style="width: 50px; height: 50px;" alt="Notification Image" class="rounded-circle me-3">
                            <div>
                                <strong>Notification 2</strong>
                                <p class="mb-0 text-muted  text-wrap small">Building status successfully changed to Approved.</p>
                            </div>
                            <span class="text-muted small ms-auto">10m ago</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <img src="{{ asset('img/buildings/building6.jpg') }}" style="width: 50px; height: 50px;" alt="Notification Image" class="rounded-circle me-3">
                            <div>
                                <strong>Notification 3</strong>
                                <p class="mb-0 text-muted text-wrap small">Building is available for Approval.</p>
                            </div>
                            <span class="text-muted small ms-auto">1h ago</span>
                        </a>
                    </div>
                    <div class="notification-footer d-flex justify-content-between p-2 px-3">
                        <button class="btn btn-primary btn-sm">Mark All as Read</button>
                        <button class="btn btn-outline-secondary btn-sm">Close</button>
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
                        style="width: 2.5rem; height: auto;">

                    <!-- User Info -->
                    <div class="d-flex flex-column text-start user-info">
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                        <small class="">{{ Auth::user()->email }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" style="z-index: 1050;" aria-labelledby="userMenu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">
                        <img src="{{ Auth::user() && Auth::user()->picture ? asset(Auth::user()->picture) : asset('img/avatar.png') }}" class="rounded-circle" alt="User Image" style="width: 1.5rem; height: auto;"> Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="fa fa-bell me-2"></i> Notifications
                            <span class="badge bg-danger badge-counter">3+</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
