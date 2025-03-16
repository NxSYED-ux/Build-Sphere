

@props([
    'breadcrumbLinks' => []
])

<nav class="navbar navbar-expand fixed-top" id="navbar_top">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        <!-- Sidenav toggler button -->
        <span class="inline-span" id="sidenav_toggler" onclick="openNav()">
            <i class='bx bx-menu-alt-left'></i>
        </span>

        <div id="top-nav-logo">
            <a href="{{ route('owner_manager_dashboard') }}" class="application-box">
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

        <!-- For Large Screen -->
        <ul class="navbar-nav navbar-lg text-center justify-content-between d-none d-md-flex ms-auto align-items-center">

            <label class="switch" style="margin-right: 1rem;">
                <input type="checkbox" id="theme-toggle">
                <span class="slider round"></span>
            </label>

            <!-- Profile Menu -->
            <x-top-nav-profile-menu
                :profileRoute="route('owner.profile')"
                :settingsRoute="'#'"
                :logoutRoute="route('logout')"
            />

            <!-- Notification Menu -->
            <x-top-nav-notification-menu />

        </ul>


        <!-- For small Screen -->
        <ul class="navbar-nav navbar-sm text-center d-md-none ms-auto">
            <!-- Profile Menu -->
            <x-top-nav-profile-menu
                :profileRoute="route('owner.profile')"
                :settingsRoute="'#'"
                :logoutRoute="route('logout')"
            />
        </ul>
    </div>
</nav>

@push('scripts')
    <script>
        document.querySelector('#sidenav_toggler').addEventListener('mouseover', function () {
            this.querySelector('i').classList.replace('bx-menu-alt-left', 'bx-menu');
        });
        document.querySelector('#sidenav_toggler').addEventListener('mouseout', function () {
            this.querySelector('i').classList.replace('bx-menu', 'bx-menu-alt-left');
        });
    </script>

@endpush






