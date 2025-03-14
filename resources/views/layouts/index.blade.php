<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <title>@yield('title', 'My App')</title>


    <script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


    <!-- Alpine.js for Mobile Menu -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">

    <script>
        (function() {
            // Check if 'darkMode' is set in localStorage, if not, default to dark mode
            if (localStorage.getItem('darkMode') === null) {
                localStorage.setItem('darkMode', 'true');
            }
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <style>
        .container {
            max-width: 1200px; /* Ensure consistent max-width */
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Video Background */
        #bg-video {
            position: fixed;
            top: 65px;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        /* Content Styling */
        #main {
            position: relative;
            z-index: 10;
        }

    </style>

    @stack('styles')

</head>
<body class="bg-white text-black dark:bg-slate-900 dark:text-white">  <!-- class="bg-slate-900" -->

<!-- Navbar -->
<header id="navbar" x-data="{ open: false }" class="transition-all z-50 duration-300 bg-white dark:bg-slate-900 pointer-events-auto">
    <nav class="container mx-auto flex items-center justify-between px-6 py-2 lg:px-8" aria-label="Global">
        <!-- Left: Logo -->
        <div class="flex lg:flex-1 items-center space-x-3">
            <a href="" class="-m-1.5 p-1.5 flex items-center">
                <span class="sr-only">AISD</span>
                <img class="w-12 h-12" src="{{ asset('logo/logo.png') }}" alt="Logo">
                <span class="text-lg font-bold text-indigo-600 ml-3">AISD</span>
            </a>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden lg:flex lg:gap-x-12">
            <a href="" class="text-sm font-semibold text-gray-900 dark:text-white">Home</a>
            <a href="" class="text-sm font-semibold text-gray-900 dark:text-white">Courses</a>
            <a href="" class="text-sm font-semibold text-gray-900 dark:text-white">Blogs</a>
            <a href="" class="text-sm font-semibold text-gray-900 dark:text-white">About Us</a>
            <a href="" class="text-sm font-semibold text-gray-900 dark:text-white">Contact Us</a>
        </div>

        <!-- Mobile Menu Button -->
        <div class="flex lg:hidden">
            <button @click="open = !open" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                <span class="sr-only">Open main menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>

        <!-- Login Button -->
        <div class="hidden lg:flex lg:flex-1 lg:justify-end">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-900 dark:text-white">Log in <span aria-hidden="true">&rarr;</span></a>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition  class="lg:hidden bg-white/20 shadow-md absolute w-full left-0 flex flex-col items-center py-6 z-50">
        <div class="w-full max-w-xs bg-white shadow-lg rounded-lg py-2">
            <a href="" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                Home
            </a>
            <a href="" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                Courses
            </a>
            <a href="" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                Blogs
            </a>
            <a href="" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                About Us
            </a>
            <a href="" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                Contact Us
            </a>
            <div class="mt-1">
                <a href="{{ route('login') }}" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                    Log in
                </a>
            </div>
        </div>
    </div>


</header>


<!-- Script to toggle dark mode -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('chk');
        // Set checkbox initial state based on localStorage
        if (localStorage.getItem('darkMode') === 'true') {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
        // Listen for changes on the checkbox
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const navbar = document.getElementById("navbar");
        const spacer = document.createElement("div");
        spacer.style.height = navbar.offsetHeight + "px";

        window.addEventListener("scroll", function () {
            if (window.scrollY > 2) {
                navbar.classList.add("fixed", "top-0", "left-0", "w-full", "shadow-md", "bg-white");
                navbar.classList.remove("relative", "bg-white/20", "backdrop-blur-lg");
                if (!spacer.parentNode) navbar.parentNode.insertBefore(spacer, navbar.nextSibling);
            } else {
                navbar.classList.add("relative", "bg-white/20", "backdrop-blur-lg"); // Ensures navbar is interactive
                navbar.classList.remove("fixed", "top-0", "left-0", "w-full", "shadow-md", "bg-white");
                if (spacer.parentNode) spacer.parentNode.removeChild(spacer);
            }
        });
    });


</script>

@stack('scripts')

</body>
</html>
