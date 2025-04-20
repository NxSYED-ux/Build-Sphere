<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('logos/Light-theme-Logo.svg') }}">
    <link id="theme-stylesheet" rel="stylesheet" href="{{ asset('css/light.css') }}">
    <title>@yield('title', 'My App')</title>
    <meta name="description" content="HMS is among the best property management companies, offering expert building management and professional property management services for residential and commercial properties. We take the hassle out of ownership with efficient rent collection, maintenance, compliance, and more—maximizing your property's value and performance.">
    <meta name="keywords" content="property management, building management, professional property management, rent collection, residential property services, commercial property services, HMS">
    <meta name="robots" content="index, follow">
    <meta name="author" content="HMS Team">

    <!-- Open Graph for Facebook and LinkedIn -->
    <meta property="og:title" content="HMS | Expert Property Management Services">
    <meta property="og:description" content="Top-tier property management services for residential and commercial properties. Hassle-free rent collection, maintenance, and more.">
    <meta property="og:image" content="{{ asset('logos/Light-theme-Logo.svg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="HMS | Expert Property Management Services">
    <meta name="twitter:description" content="Residential and commercial property management done right. Rent, maintain, and grow with HMS.">
    <meta name="twitter:image" content="{{ asset('logos/Light-theme-Logo.svg') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="role-id" content="{{ auth()->user()->role_id }}">
    <meta name="is-super-admin" content="{{ auth()->user()->is_super_admin }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Firebase Meta Tags -->
    <meta name="firebase-api-key" content="{{ config('firebase.api_key') }}">
    <meta name="firebase-auth-domain" content="{{ config('firebase.auth_domain') }}">
    <meta name="firebase-project-id" content="{{ config('firebase.project_id') }}">
    <meta name="firebase-messaging-sender-id" content="{{ config('firebase.messaging_sender_id') }}">
    <meta name="firebase-app-id" content="{{ config('firebase.app_id') }}">
    <meta name="firebase-vapid-key" content="{{ config('firebase.vapid_key') }}">

    <!-- Pusher Meta Tags -->
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">



    <script>
        window.initialPermissions = @json(session('permissions', []));
    </script>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/topnavbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidenavbar.css') }}" rel="stylesheet">

    <script src="{{ asset('js/pusher.js') }}"></script>
    <!-- Include SweetAlert2 CSS from CDN -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
    <!-- Add DataTables CSS link -->
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Add DataTables Buttons CSS link -->
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Add Font Awesome 4.7 CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Box Icons css link -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Families -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <script>
        (function() {
            const themeStylesheet = document.getElementById('theme-stylesheet');
            const savedTheme = localStorage.getItem('theme') || 'light'; // Default to light

            if (savedTheme === 'dark') {
                themeStylesheet.setAttribute('href', '{{ asset('css/dark.css') }}');
                document.documentElement.classList.add('dark');
            } else {
                themeStylesheet.setAttribute('href', '{{ asset('css/light.css') }}');
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <style>
        body {
            font-family: 'Poppins', CircularXX, sans-serif;
            font-size: 14px;
            font-style: normal !important;
            position: relative;
            background-color: var(--body-background-color);
        }
        #main{
            font-family: 'Poppins', CircularXX, sans-serif;
            font-style: normal !important;
            transition: margin-left 0.3s;
            margin: 0;
            margin-left: 0;
            padding-top: 13px;
            overflow-y: auto;
            background-color: var(--main-background-color);
            color: var(--main-text-color);
        }

        .hidden {
            display: none !important;
        }


        @media screen and (min-width: 769px) {
            #main {
                margin-left: 250px;
            }
        }

        .content-top{
            border-top: 5px solid var(--breadcrumb-text2-color);
        }

        /*  Body Cards  */
        .card{
            background-color: var(--body-card-bg) !important;)
        }

        .card-body{
            color: var(--main-text-color) !important;
        }

        .card label{
            color: var(--body-card-label-color) !important;
        }
        .card span{
            color: var(--body-card-span-color) !important;
        }

        th{
            background-color: var(--th-bg) !important;
            color: var(--th-color) !important;
        }

        td{
            background-color: var(--td-bg) !important;
            color: var(--td-color) !important;
        }

        label{
            font-size: 14px;
            /*font-weight: bold;*/
            color: var(--label-color);
        }

        input:not(.form-check-input):not(.is-invalid),
        select:not(.is-invalid),
        textarea:not(.is-invalid) {
            background-color: var(--input-bg-color) !important;
            color: var(--input-text-color) !important;
            border: 1px solid var(--input-border-color) !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
            color: var(--input-text-color) !important;
            background-color: var(--input-bg-color) !important;
        }

        form input i{
            color: var(--input-icon-color) !important;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(var(--invert, 0));
        }

        input:focus, select:focus, textarea:focus {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
        }

        input::placeholder, textarea::placeholder {
            color: var(--placeholder-color) !important;
        }

        input:focus::placeholder, textarea:focus::placeholder {
            color: #6c757d;
        }

        .toast-progress {
            height: 3px;
            width: 100%;
            background-color: #0d6efd;
            position: absolute;
            bottom: 0;
            right: 0; /* Align the progress bar to the right */
            animation: progressBar 10s linear forwards;
        }

        @keyframes progressBar {
            from {
                width: 100%;
                right: 0;
            }
            to {
                width: 0%;
                right: 0;
            }
        }

        .btn-primary{
            border-color: var(--breadcrumb-text2-color);
            color: #fff;
            background-color: var(--breadcrumb-text2-color);
        }

        .btn-primary:hover{
            border-color: var(--breadcrumb-text2-color);
            color: #fff;
            background-color: var(--breadcrumb-text2-color);
            opacity: 0.9;
        }

        .btn-outline-primary{
            color: var(--btn-outline-primary) !important;
            background-color: var(--btn-outline-bg-primary) !important;
            border-color: var(--btn-outline-border-primary) !important;
        }

        .btn-outline-primary:hover{
            color: var(--btn-hover-outline-primary) !important;
            background-color: var(--btn-hover-outline-bg-primary) !important;
            border-color: var(--btn-hover-outline-border-primary) !important;
        }

        .btn-secondary {
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            background-color: #6c757d;
            color: #ffff;
        }

        .text-primary{
            -webkit-text-fill-color: var(--breadcrumb-text2-color);
        }

        @media (max-width: 576px) {
            .custom-pagination-wrapper {
                display: flex;
                justify-content: center;
            }
        }

    </style>

    @stack('styles')

</head>
<body>

    <x-real-time-notifications />
    <x-loading-animation />
    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Include jQuery library -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
    <!-- Include SweetAlert2 JS from CDN -->
    <script src="{{ asset('js/sweetalert.js') }}"></script>




    <!-- Firebase SDKs -->
    <script src="{{ asset('js/firebase-app-compat.js') }}"></script>
    <script src="{{ asset('js/firebase-messaging-compat.js') }}"></script>

    <script>
        function getMeta(name) {
            return document.querySelector(`meta[name="${name}"]`)?.getAttribute('content') || '';
        }

        window.FIREBASE_CONFIG = {
            apiKey: getMeta('firebase-api-key'),
            authDomain: getMeta('firebase-auth-domain'),
            projectId: getMeta('firebase-project-id'),
            messagingSenderId: getMeta('firebase-messaging-sender-id'),
            appId: getMeta('firebase-app-id'),
            vapidKey: getMeta('firebase-vapid-key')
        };

        window.PUSHER_CONFIG = {
            appKey: getMeta('pusher-key'),
            appCluster: getMeta('pusher-cluster')
        };
    </script>

    <!-- Your Notification Script -->
    <script src="{{ asset('js/appjs/notifications.js') }}"></script>


    <script src="{{ asset('js/appjs/permissions.js') }}"></script>


    <!-- Toogle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <script>
        function applyTheme() {
            const themeToggle = document.getElementById('theme-toggle');
            const themeStylesheet = document.getElementById('theme-stylesheet');
            const savedTheme = localStorage.getItem('theme') || 'light'; // Default to light

            if (savedTheme === 'dark') {
                themeStylesheet.setAttribute('href', '{{ asset('css/dark.css') }}');
                themeToggle.checked = true;
            } else {
                themeStylesheet.setAttribute('href', '{{ asset('css/light.css') }}');
                themeToggle.checked = false;
            }
        }

        document.addEventListener('DOMContentLoaded', applyTheme);

        // Toggle theme when the user changes the switch
        document.getElementById('theme-toggle').addEventListener('change', function() {
            const themeStylesheet = document.getElementById('theme-stylesheet');

            if (this.checked) {
                localStorage.setItem('theme', 'dark');
                themeStylesheet.setAttribute('href', '{{ asset('css/dark.css') }}');
            } else {
                localStorage.setItem('theme', 'light');
                themeStylesheet.setAttribute('href', '{{ asset('css/light.css') }}');
            }
        });
    </script>

    @stack('scripts')

</body>
</html>
