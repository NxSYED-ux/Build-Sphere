
<style>
    #navbar .application-box {
        flex-shrink: 0;
        width: 100%;
        height: 58px;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    #navbar .application-logo {
        width: 62px;
        height: auto;
    }

    #navbar .application-text {
        display: flex;
        flex-direction: column;
        margin-left: 7px;
    }

    #navbar .application-logo-text {
        font-size: 20px;
        font-weight: bold;
        color: var(--sidenavbar-text-color);
    }

    #navbar .application-tagline {
        font-size: 8px;
        color: var(--sidenavbar-text-color);
        opacity: 0.8;
    }
</style>

<header id="navbar" x-data="{ open: false }" class="transition-all z-50 duration-300 bg-white dark:bg-slate-900 pointer-events-auto">
    <nav class="container mx-auto flex items-center justify-between px-6 py-2 lg:px-8" aria-label="Global">
        <!-- Left: Logo -->
        <div class="flex lg:flex-1 items-center space-x-3">
{{--            <a href="#" class="-m-1.5 p-1.5 flex items-center">--}}
{{--                <img class="w-12 h-12" src="{{ asset('logos/Light-theme-Logo.svg') }}" alt="Logo">--}}
{{--            </a>--}}

            <div id="navbar-logo">
                <a href="#" class="application-box">
                    <img class="application-logo" src="{{ asset('logos/Light-theme-Logo.svg') }}" alt="Logo">

                    <div class="application-text">
                        <span class="application-logo-text">HMS</span>
                        <span class="application-tagline">Elevate Your Living</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden lg:flex lg:gap-x-12">
            <a href="#" class="text-sm font-semibold text-gray-900 dark:text-white">Home</a>
            <a href="#" class="text-sm font-semibold text-gray-900 dark:text-white">About Us</a>
            <a href="#" class="text-sm font-semibold text-gray-900 dark:text-white">Contact Us</a>
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
            <a href="#" class="text-sm font-semibold text-gray-900 dark:text-white">Log in <span aria-hidden="true">&rarr;</span></a>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition  class="lg:hidden bg-white/20 shadow-md absolute w-full left-0 flex flex-col items-center py-6 z-50">
        <div class="w-full max-w-xs bg-white shadow-lg rounded-lg py-2">
            <a href="#" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                Home
            </a>
            <a href="#" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                About Us
            </a>
            <a href="#" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                Contact Us
            </a>
            <div class="mt-1">
                <a href="#" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                    Log in
                </a>
            </div>
        </div>
    </div>
</header>
