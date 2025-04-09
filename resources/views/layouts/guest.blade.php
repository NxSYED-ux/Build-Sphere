<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('logos/Light-theme-Logo.svg') }}">
    <link id="theme-stylesheet" rel="stylesheet" href="{{ asset('css/light.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My App')</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <!-- Include SweetAlert2 CSS from CDN -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
    <!-- Add Font Awesome 4.7 CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
            color: lightgray;
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

    </style>

    @stack('styles')

</head>
<body>
<x-loading-animation />
<!-- Main Content -->
<div class="content">
    @yield('content')
</div>

<!-- Include SweetAlert2 JS from CDN -->
<script src="{{ asset('js/sweetalert.js') }}"></script>

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
