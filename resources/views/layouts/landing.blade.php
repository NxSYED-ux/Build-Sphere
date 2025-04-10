<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('logos/Light-theme-Logo.svg') }}">
    <script src="{{ asset('js/tailwind.js') }}"></script>
    <title>@yield('title', 'My App')</title>

    <!-- Alpine.js for Mobile Menu -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
</head>
<body>

@stack('styles')
<div class="content">
    @yield('content')
</div>

@stack('scripts')
<body>
