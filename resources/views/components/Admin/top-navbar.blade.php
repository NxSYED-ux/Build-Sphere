
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
            <svg  width="48" height="48" class="application-logo" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_27_397)">
                <path d="M48.0959 37.8242C48.0959 41.2461 48.0959 44.668 48.0959 48.0931C32.1078 48.0931 16.1196 48.0931 0.0959473 48.0931C0.0959473 44.7022 0.0959473 41.308 0.140269 37.8682C0.540792 37.8179 0.896992 37.8132 1.29751 37.8142C3.26006 37.8134 5.1783 37.8069 7.12789 37.7934C7.27247 37.6154 7.39548 37.4499 7.49745 37.2724C8.77745 35.0439 10.0506 32.8114 11.3332 30.5844C12.8699 27.916 14.4133 25.2513 15.9556 22.586C16.4739 21.6902 16.9979 20.7977 17.5404 19.8674C17.623 19.9922 17.6788 20.0675 17.7252 20.1482C18.5 21.4963 19.2717 22.8462 20.0478 24.1936C21.6934 27.0508 23.3405 29.9071 24.9884 32.7631C25.8766 34.3025 26.783 35.8318 27.6464 37.385C27.863 37.7747 28.1131 37.8998 28.5614 37.811C30.3643 37.8143 32.1229 37.8113 33.9255 37.8146C34.3106 37.821 34.6518 37.821 35.0637 37.821C29.2085 27.6616 23.4029 17.5886 17.5661 7.46115C17.46 7.61098 17.3818 7.70401 17.3226 7.8079C16.6627 8.96589 16.0136 10.1301 15.3463 11.2838C13.981 13.6447 12.6017 15.9974 11.2368 18.3585C9.40944 21.5197 7.58808 24.6843 5.76834 27.8499C4.00539 30.9166 2.2496 33.9875 0.482329 37.0517C0.394136 37.2046 0.22656 37.3117 0.0959473 37.4402C0.0959473 24.9964 0.0959473 12.5527 0.0959473 0.102539C16.0863 0.102539 32.0766 0.102539 48.0959 0.102539C48.0959 12.5143 48.0959 24.9324 48.0703 37.4265C47.5953 36.7434 47.1424 35.9864 46.6974 35.2248C45.6634 33.455 44.6277 31.6861 43.6033 29.9107C42.7103 28.3632 41.8374 26.8042 40.9458 25.2559C39.8074 23.2791 38.6552 21.3104 37.5164 19.3339C36.4857 17.5449 35.4699 15.7472 34.4385 13.9585C33.368 12.1022 32.2866 10.2522 31.2101 8.39943C31.0289 8.08761 30.8483 7.77547 30.6334 7.4047C29.5561 9.28381 28.5337 11.0781 27.5004 12.8661C26.739 14.1837 25.9728 15.4987 25.1884 16.8025C24.9418 17.2123 24.9469 17.5596 25.2014 17.9642C25.6549 18.6851 26.0687 19.4312 26.4969 20.1679C27.1444 21.2818 27.7901 22.3967 28.4829 23.5911C29.2254 22.3165 29.9342 21.0996 30.6535 19.8649C30.9014 20.3099 31.127 20.7286 31.3653 21.14C32.2677 22.6977 33.1785 24.2506 34.0778 25.8101C34.7443 26.9661 35.3925 28.1325 36.0584 29.2888C36.8649 30.6891 37.6851 32.0813 38.4923 33.4811C39.2528 34.7999 40.0116 36.1198 40.7539 37.4489C40.924 37.7535 41.1326 37.8364 41.4733 37.8337C43.6808 37.8162 45.8884 37.8244 48.0959 37.8242Z" id="fill1"/>
                <path d="M0.0959473 37.4784C0.22656 37.3115 0.394136 37.2044 0.482329 37.0515C2.2496 33.9872 4.00539 30.9164 5.76834 27.8496C7.58808 24.6841 9.40944 21.5194 11.2368 18.3583C12.6017 15.9972 13.981 13.6444 15.3463 11.2836C16.0136 10.1299 16.6627 8.96567 17.3226 7.80768C17.3818 7.70379 17.46 7.61076 17.5661 7.46094C23.4029 17.5883 29.2085 27.6614 35.0637 37.8208C34.6518 37.8208 34.3106 37.8208 33.8927 37.7934C32.0927 37.7585 30.3695 37.7506 28.6463 37.7461C28.6033 37.746 28.5602 37.784 28.5171 37.8043C28.1131 37.8996 27.863 37.7745 27.6464 37.3848C26.783 35.8316 25.8766 34.3023 24.9884 32.7628C23.3405 29.9069 21.6934 27.0506 20.0478 24.1934C19.2717 22.846 18.5 21.4961 17.7252 20.148C17.6788 20.0672 17.623 19.9919 17.5404 19.8671C16.9979 20.7974 16.4739 21.69 15.9556 22.5858C14.4133 25.251 12.8699 27.9157 11.3332 30.5842C10.0506 32.8112 8.77745 35.0437 7.49745 37.2722C7.39548 37.4497 7.27247 37.6151 7.09429 37.7737C5.16222 37.7529 3.2951 37.744 1.42797 37.7395C1.36977 37.7394 1.31146 37.7844 1.25319 37.8084C0.896992 37.813 0.540792 37.8177 0.140269 37.8232C0.0959473 37.7216 0.0959473 37.6192 0.0959473 37.4784Z" id="fill2"/>
                <path d="M48.0961 37.7854C45.8886 37.824 43.6809 37.8158 41.4735 37.8333C41.1328 37.836 40.9242 37.7531 40.7541 37.4485C40.0118 36.1194 39.253 34.7995 38.4925 33.4807C37.6853 32.0809 36.865 30.6887 36.0586 29.2884C35.3927 28.1321 34.7445 26.9656 34.0779 25.8097C33.1787 24.2502 32.2679 22.6973 31.3655 21.1396C31.1272 20.7282 30.9016 20.3094 30.6536 19.8645C29.9344 21.0992 29.2255 22.3161 28.483 23.5907C27.7903 22.3963 27.1446 21.2814 26.4971 20.1675C26.0688 19.4308 25.6551 18.6847 25.2015 17.9638C24.947 17.5592 24.942 17.2119 25.1885 16.8021C25.973 15.4983 26.7391 14.1832 27.5005 12.8657C28.5339 11.0777 29.5563 9.28341 30.6335 7.4043C30.8485 7.77506 31.0291 8.0872 31.2103 8.39903C32.2868 10.2518 33.3682 12.1018 34.4387 13.9581C35.4701 15.7468 36.4858 17.5445 37.5166 19.3335C38.6553 21.31 39.8076 23.2787 40.9459 25.2555C41.8375 26.8038 42.7105 28.3628 43.6034 29.9103C44.6279 31.6857 45.6636 33.4546 46.6976 35.2244C47.1425 35.986 47.5955 36.743 48.0705 37.471C48.0961 37.5422 48.0961 37.6446 48.0961 37.7854Z" id="fill3"/>
                <path d="M1.29761 37.8142C1.31155 37.7846 1.36987 37.7396 1.42807 37.7397C3.2952 37.7443 5.16232 37.7531 7.06304 37.7809C5.1784 37.8069 3.26016 37.8134 1.29761 37.8142Z" id="fill4"/>
                <path d="M28.5613 37.8108C28.5601 37.7841 28.6033 37.746 28.6463 37.7461C30.3695 37.7506 32.0926 37.7586 33.8487 37.7871C32.1229 37.8111 30.3642 37.8141 28.5613 37.8108Z" id="fill5"/>
                </g>
                <defs>
                <clipPath id="clip0_27_397">
                <rect width="48" height="48" fill="white"/>
                </clipPath>
                </defs>
            </svg>

            <!-- <img src="{{ asset('icons/Logo.png') }}" loading="lazy" class="application-logo" alt="">  -->
            <span class="application-logo-text">HEIGHTS</span>
        </a>

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
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin_profile') }}">
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
                        <a class="dropdown-item" href="{{ route('admin_profile') }}">
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
