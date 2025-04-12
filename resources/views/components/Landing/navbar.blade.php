
<style>
    #navbar .application-box {
        flex-shrink: 0;
        width: 100%;
        height: 58px;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    #navbar .container {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 1rem;
        padding-right: 1rem;
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
        font-size: 9px;
        color: var(--sidenavbar-text-color);
        opacity: 0.8;
    }
</style>

<header id="navbar" x-data="{ open: false, loginMenu: false, mobileLoginMenu: false }" class="fixed top-0 left-0  w-full transition-all z-50 duration-300 bg-white  pointer-events-auto">
    <nav class="container mx-auto flex items-center justify-between px-6 py-2 lg:px-8" aria-label="Global">
        <!-- Left: Logo -->
        <div class="flex lg:flex-1 items-center space-x-3">
            <div id="navbar-logo">
                <a href="{{ route('index') }}" class="application-box">
                    <img class="application-logo" src="{{ asset('logos/Light-theme-Logo.svg') }}" alt="Logo">
                    <div class="application-text">
                        <span class="application-logo-text">HMS</span>
                        <span class="application-tagline">Rise With HMS</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden lg:flex lg:gap-x-12">
            <a href="{{ route('index') }}" class="text-sm font-semibold text-gray-900 ">Home</a>
            <a href="#" class="text-sm font-semibold text-gray-900 ">About Us</a>
            <a href="#" class="text-sm font-semibold text-gray-900 ">Contact Us</a>
        </div>

        <!-- Mobile Menu Button -->
        <div class="flex lg:hidden">
            <button @click="open = !open" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                <span class="sr-only">Toggle menu</span>
                <!-- Hamburger Icon -->
                <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <!-- Cross (X) Icon -->
                <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Login/Signup Section (Desktop) -->
        <div class="hidden  lg:flex lg:flex-1 lg:justify-end lg:space-x-4 lg:ml-auto">
            <!-- Login -->
            <a href="{{ route('login') }}"
               class="text-sm font-medium text-gray-700 hover:text-gray-900 pt-2 transition-colors duration-200">
                Log In
            </a>

            <!-- Divider -->
            <span class="h-9 w-px bg-gray-300"></span>

            <!-- Sign Up Button -->
            <a href="{{ route('signUp') }}"
               class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition-colors duration-200">
                Sign Up
            </a>
        </div>


    </nav>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition class="lg:hidden bg-white/20 shadow-md absolute w-full left-0 flex flex-col items-center py-6 z-50">
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
            <a href="{{ route('login') }}" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                Log In
            </a>
            <a href="{{ route('signUp') }}" class="block text-center px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">
                Sign Up
            </a>

        </div>
    </div>
</header>
