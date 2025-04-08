
@props(['openSections' => []])

@php
    $permissions = json_decode(request('permissions', '{}'), true);
@endphp

<!-- &times; -->
<div id="mySidenav" class="sidenav" >

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
            <span class="application-tagline">Rise With HMS</span>
        </div>
    </a>

    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">
        <i class='bx bx-menu-alt-right'></i>
    </a>

        <div class="scrollable-content pt-4">
            <ul class=" ps-0 ">

                <!-- Dashboard -->
                <li class="mb-1">
                    <div id="link-a">
                        <a href="{{ route('owner_manager_dashboard') }}" class="link-dark collapsed {{ in_array('Dashboard', $openSections) ? 'Link-background-color' : '' }} px-1" id="AdminDashboardbtn" >
                        <i class="bx bxs-dashboard icons"></i> Dashboard </a>
                    </div>
                </li>

                <!-- Buildings -->
                <li class="mb-1 hidden" id="OwnerBuildingss">
                    <div id="link-a">
                        <a href="#"
                            class="link-dark link-toggle collapsed d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse" data-bs-target="#Buildings" aria-expanded="{{ in_array('Buildings', $openSections) ? 'true' : 'false' }}"
                            onclick="toggleSection('Buildings')">
                            <span class="d-flex align-items-center">
                                <i class="bx bx-buildings icons"></i> <!-- Adjusted with spacing -->
                                <span>Buildings</span>
                            </span>
                            <i class="fa fa-chevron-left  chevron-icon" style="cursor: pointer;"></i>
                        </a>
                    </div>

                    <div class="collapse {{ in_array('Buildings', $openSections) ? 'show' : '' }}" id="Buildings" style="">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li  id="OwnerBuildings" class="hidden"><a href="{{ route('owner.buildings.index') }}" class="link-dark {{ in_array('Building', $openSections) ? 'Link-background-color' : '' }}" data-bs-target="#Buildings "><i class="bx bx-buildings icons"></i> Buildings</a></li>
                            <li  id="OwnerLevels" class="hidden"><a href="{{ route('owner.levels.index') }}" class="link-dark {{ in_array('Levels', $openSections) ? 'Link-background-color' : '' }}" data-bs-target="#AdminControl" ><i class="bx bxs-city icons"></i> Levels</a></li>
                            <li  id="OwnerUnits" class="hidden"><a href="{{ route('owner.units.index') }}" class="link-dark {{ in_array('Units', $openSections) ? 'Link-background-color' : '' }}" data-bs-target="#AdminControl" ><i class="bx bxs-home icons"></i> Units</a></li>
                        </ul>
                    </div>

                </li>

                <!-- Assign Units -->
                <li class="mb-1 hidden" id="OwnerAssignUnits">
                    <div id="link-a">
                        <a href="{{ route('owner.assignunits.index') }}" class="link-dark collapsed {{ in_array('Assign-Units', $openSections) ? 'Link-background-color' : '' }} px-1" id="BuildingsTreebtn" >
                        <i class="bx bxs-user-check icons"></i> Assign Units </a>
                    </div>
                </li>

                <!-- Buildings Tree -->
                <li class="mb-1 hidden"  id="OwnerBuildingsTree">
                    <div id="link-a">
                        <a href="{{ route('owner.buildings.tree') }}" class="link-dark collapsed {{ in_array('Buildings-Tree', $openSections) ? 'Link-background-color' : '' }} px-1" id="BuildingsTreebtn" >
                        <i class="bx bx-network-chart icons"></i> Buildings Tree </a>
                    </div>
                </li>

                <!-- Rentals -->
                <li class="mb-1 hidden" id="OwnerRentals">
                    <div id="link-a">
                        <a href="" class="link-dark collapsed {{ in_array('Rentals', $openSections) ? 'Link-background-color' : '' }} px-1" id="Organizationbtn" >
                        <i class="bx bxs-business icons"></i> Rentals </a>
                    </div>
                </li>

                <!-- Memberships -->
                <li class="mb-1 hidden" id="OwnerMemberships">
                    <div id="link-a">
                        <a href="#" class="link-dark collapsed {{ in_array('Memberships', $openSections) ? 'Link-background-color' : '' }} px-1" id="Membershipsbtn" >
                        <i class='bx bxs-credit-card-front icons' ></i> Memberships </a>
                    </div>
                </li>

                <!-- Department -->
                <li class="mb-1 hidden" id="OwnerDepartments">
                    <div id="link-a">
                        <a href="#" class="link-dark collapsed {{ in_array('Departments', $openSections) ? 'Link-background-color' : '' }} px-1" id="Departmentsbtn" >
                        <i class='bx bx-git-repo-forked icons'></i> Departments </a>
                    </div>
                </li>

                <!-- Staff -->
                <li class="mb-1 hidden" id="OwnerStaff">
                    <div id="link-a">
                        <a href="#" class="link-dark collapsed {{ in_array('Staff', $openSections) ? 'Link-background-color' : '' }} px-1" id="Ownerbtn" >
                        <i class="bx bx-user icons"></i> Staff </a>
                    </div>
                </li>

                 <!-- Reports -->
                 <li class="mb-1 hidden" id="OwnerReports">
                    <div id="link-a">
                        <a href="#" class="link-dark collapsed {{ in_array('Reports', $openSections) ? 'Link-background-color' : '' }} px-1" id="Reportsbtn" >
                        <i class="bx bxs-pie-chart-alt-2 icons"></i> Reports </a>
                    </div>
                </li>

            </ul>

        </div>
    </div>

{{--<script>--}}
{{--    document.addEventListener("DOMContentLoaded", function () {--}}
{{--        let sessionPermissions = @json(session('permissions', []));--}}

{{--        if (Object.keys(sessionPermissions).length > 0) {--}}
{{--            localStorage.setItem("userPermissions", JSON.stringify(sessionPermissions));--}}
{{--        }--}}
{{--        applyPermissions();--}}
{{--        watchLocalStorage();--}}
{{--    });--}}

{{--    function applyPermissions() {--}}
{{--        let storedPermissions = JSON.parse(localStorage.getItem("userPermissions")) || {};--}}

{{--        if(storedPermissions['Owner Portal']) {--}}

{{--            // Buildings--}}
{{--            if (storedPermissions['Owner Portal'].includes('Owner Buildings') || storedPermissions['Owner Portal'].includes('Owner Levels') || storedPermissions['Owner Portal'].includes('Owner Units')) {--}}
{{--                document.getElementById("OwnerBuildingss").style.display = "block";--}}
{{--                if(storedPermissions['Owner Portal'].includes('Owner Buildings')){--}}
{{--                    document.getElementById("OwnerBuildings").style.display = "block";--}}
{{--                } else {--}}
{{--                    document.getElementById("OwnerBuildings").style.display = "none";--}}
{{--                }--}}
{{--                if(storedPermissions['Owner Portal'].includes('Owner Levels')){--}}
{{--                    document.getElementById("OwnerLevels").style.display = "block";--}}
{{--                } else {--}}
{{--                    document.getElementById("OwnerLevels").style.display = "none";--}}
{{--                }--}}
{{--                if(storedPermissions['Owner Portal'].includes('Owner Units')){--}}
{{--                    document.getElementById("OwnerUnits").style.display = "block";--}}
{{--                } else {--}}
{{--                    document.getElementById("OwnerUnits").style.display = "none";--}}
{{--                }--}}
{{--            } else {--}}
{{--                document.getElementById("OwnerBuildingss").style.display = "none";--}}
{{--            }--}}

{{--            // Owner Assign Units--}}
{{--            if (storedPermissions['Owner Portal'].includes('Owner Assign Units')) {--}}
{{--                document.getElementById("OwnerAssignUnits").style.display = "block";--}}
{{--            } else {--}}
{{--                document.getElementById("OwnerAssignUnits").style.display = "none";--}}
{{--            }--}}

{{--            // Owner Buildings Tree--}}
{{--            if (storedPermissions['Owner Portal'].includes('Owner Buildings Tree')) {--}}
{{--                document.getElementById("OwnerBuildingsTree").style.display = "block";--}}
{{--            } else {--}}
{{--                document.getElementById("OwnerBuildingsTree").style.display = "none";--}}
{{--            }--}}

{{--            // Owner Rentals--}}
{{--            if (storedPermissions['Owner Portal'].includes('Owner User Units')) {--}}
{{--                document.getElementById("OwnerRentals").style.display = "block";--}}
{{--            } else {--}}
{{--                document.getElementById("OwnerRentals").style.display = "none";--}}
{{--            }--}}

{{--            // Owner Departments--}}
{{--            if (storedPermissions['Owner Portal'].includes('Owner Departments')) {--}}
{{--                document.getElementById("OwnerDepartments").style.display = "block";--}}
{{--            } else {--}}
{{--                document.getElementById("OwnerDepartments").style.display = "none";--}}
{{--            }--}}

{{--            // Owner Memberships--}}
{{--            if (storedPermissions['Owner Portal'].includes('Owner Memberships')) {--}}
{{--                document.getElementById("OwnerMemberships").style.display = "block";--}}
{{--            } else {--}}
{{--                document.getElementById("OwnerMemberships").style.display = "none";--}}
{{--            }--}}

{{--            // Owner Staff--}}
{{--            if (storedPermissions['Owner Portal'].includes('Owner Staff')) {--}}
{{--                document.getElementById("OwnerStaff").style.display = "block";--}}
{{--            } else {--}}
{{--                document.getElementById("OwnerStaff").style.display = "none";--}}
{{--            }--}}

{{--            // Owner Reports--}}
{{--            if (storedPermissions['Owner Portal'].includes('Owner Reports')) {--}}
{{--                document.getElementById("OwnerReports").style.display = "block";--}}
{{--            } else {--}}
{{--                document.getElementById("OwnerReports").style.display = "none";--}}
{{--            }--}}
{{--        }--}}

{{--    }--}}

{{--    function watchLocalStorage() {--}}
{{--        window.addEventListener("storage", function (event) {--}}
{{--            if (event.key === "userPermissions") {--}}
{{--                applyPermissions();--}}
{{--            }--}}
{{--        });--}}
{{--    }--}}
{{--</script> --}}

<script>
    document.querySelector('.closebtn').addEventListener('mouseover', function () {
        this.querySelector('i').classList.replace('bx-menu-alt-right', 'bx-menu');
    });

    document.querySelector('.closebtn').addEventListener('mouseout', function () {
        this.querySelector('i').classList.replace('bx-menu', 'bx-menu-alt-right');
    });


</script>

<script>
    window.addEventListener("load", function () {
        if (window.innerWidth >= 768) { // Check if the screen width is equal to or greater than 768px (considered "md" or greater)
            document.getElementById("mySidenav").style.width = "250px";
            document.getElementById("main").style.marginLeft = "250px";
            document.getElementById("navbar_top").style.marginLeft = "250px";
            var togglerElement = document.getElementById("sidenav_toggler");
            if (togglerElement) {
                togglerElement.style.display = 'none';
            }
        }
    });


    function openNav() {
        // Check if the screen width is greater than a certain threshold (e.g., 768px)
        if (window.innerWidth > 768) {
            document.getElementById("mySidenav").style.width = "250px";
            document.getElementById("main").style.marginLeft = "250px";
            document.getElementById("navbar_top").style.marginLeft = "250px";
            var togglerElement = document.getElementById("sidenav_toggler");
            var topNavLogo = document.getElementById("top-nav-logo");
            var closeBtn = document.getElementsByClassName("closebtn")[0];
            if (togglerElement) {
                togglerElement.style.display = 'none';
            }
            if(closeBtn){
                closeBtn.style.display = 'block';
            }
            topNavLogo.style.display = 'none';

            document.body.classList.remove("sidebar-closed");
        } else {
            // For smaller screens, only open the sidebar without changing main's margin
            document.getElementById("mySidenav").style.width = "250px";
            document.getElementById("main").style.marginLeft = "0";
            document.getElementById("navbar_top").style.marginLeft = "250px";
            var togglerElement = document.getElementById("sidenav_toggler");
            var topNavLogo = document.getElementById("top-nav-logo");
            var closeBtn = document.getElementsByClassName("closebtn")[0];
            if (togglerElement) {
                togglerElement.style.display = 'none';
            }
            if(closeBtn){
                closeBtn.style.display = 'block';
            }
            topNavLogo.style.display = 'none';

            document.body.classList.remove("sidebar-closed");
        }
    }

    function closeNav() {
        if (window.innerWidth > 768) {
            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
            document.getElementById("navbar_top").style.marginLeft = "0";

            var togglerElement = document.getElementById("sidenav_toggler");
            var topNavLogo = document.getElementById("top-nav-logo");
            var closeBtn = document.getElementsByClassName("closebtn")[0];

            if (togglerElement) {
                togglerElement.style.display = 'block';
            }
            if (closeBtn) {
                closeBtn.style.display = 'none';
            }
            topNavLogo.style.display = 'block';

            // Add class to body when sidebar is closed
            document.body.classList.add("sidebar-closed");
        } else {
            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
            document.getElementById("navbar_top").style.marginLeft = "0";

            var togglerElement = document.getElementById("sidenav_toggler");
            var topNavLogo = document.getElementById("top-nav-logo");
            var closeBtn = document.getElementsByClassName("closebtn")[0];

            if (togglerElement) {
                togglerElement.style.display = 'block';
            }
            if (closeBtn) {
                closeBtn.style.display = 'none';
            }
            topNavLogo.style.display = 'block';

            // Add class to body when sidebar is closed
            document.body.classList.add("sidebar-closed");
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get all collapse toggle links with chevrons
        const toggleLinks = document.querySelectorAll('[data-bs-toggle="collapse"]');

        toggleLinks.forEach(link => {
            const targetId = link.getAttribute('data-bs-target'); // Get the collapse target
            const targetElement = document.querySelector(targetId);
            const chevronIcon = link.querySelector('.chevron-icon');

            // Check initial state of collapse and set chevron accordingly
            if (targetElement.classList.contains('show')) {
                chevronIcon.classList.remove('fa-chevron-left');
                chevronIcon.classList.add('fa-chevron-down');
            } else {
                chevronIcon.classList.remove('fa-chevron-down');
                chevronIcon.classList.add('fa-chevron-left');
            }

            // Add event listeners for collapse events
            targetElement.addEventListener('show.bs.collapse', () => {
                chevronIcon.classList.remove('fa-chevron-left');
                chevronIcon.classList.add('fa-chevron-down');
            });

            targetElement.addEventListener('hide.bs.collapse', () => {
                chevronIcon.classList.remove('fa-chevron-down');
                chevronIcon.classList.add('fa-chevron-left');
            });
        });
    });


</script>
