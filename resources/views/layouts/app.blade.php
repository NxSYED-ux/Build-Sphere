<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add Bootstrap 5.3.3 CSS link -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- <link id="theme-stylesheet" rel="stylesheet" href="{{ asset('css/light.css?v=' . time()) }}"> -->
    <link id="theme-stylesheet" rel="stylesheet" href="{{ asset('css/light.css') }}">

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


    <link href="{{ asset('css/topnavbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidenavbar.css') }}" rel="stylesheet">
    <!-- Include SweetAlert2 CSS from CDN -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">

    <!-- Add DataTables CSS link -->
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Add DataTables Buttons CSS link -->
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- Add Font Awesome 4.7 CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Box Icons css link -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <title>@yield('title', 'My App')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">


    <style>
        body {
            font-family: 'Poppins', CircularXX, sans-serif;
            /*font-family: CircularXX,sans-serif;*/
            font-size: 14px;
            font-style: normal !important;
            position: relative;
            background-color: var(--body-background-color);
        }
        #main{
            font-family: 'Poppins', CircularXX, sans-serif;
            /*font-family: CircularXX,sans-serif !important;*/
            font-style: normal !important;
            transition: margin-left 0.3s;
            margin: 0;
            margin-left: 0;
            padding-top: 13px;
            overflow-y: auto;
            background-color: var(--main-background-color);
            color: var(--main-text-color);
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



    </style>
    @stack('styles')
</head>
<body>

    <x-loading-animation />
    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Add Bootstrap 5.1.3 JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- Include jQuery library -->
    <script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
    <!-- Include SweetAlert2 JS from CDN -->
    <script src="{{ asset('js/sweetalert.js') }}"></script>

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
        // Apply theme based on localStorage immediately
        function applyTheme() {
            const themeToggle = document.getElementById('theme-toggle');
            const themeStylesheet = document.getElementById('theme-stylesheet');
            const savedTheme = localStorage.getItem('theme') || 'light'; // Default to light

            if (savedTheme === 'dark') {
                themeStylesheet.setAttribute('href', '{{ asset('css/dark.css') }}');
                themeToggle.checked = true;  // Set toggle to dark mode
            } else {
                themeStylesheet.setAttribute('href', '{{ asset('css/light.css') }}');
                themeToggle.checked = false; // Set toggle to light mode
            }
        }

        // Run theme applying as soon as DOM is ready
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
