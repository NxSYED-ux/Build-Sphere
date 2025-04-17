<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('logos/Light-theme-Logo.svg') }}">
    <script src="{{ asset('js/tailwind.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My App')</title>
    <meta name="description" content="HMS is among the best property management companies, offering expert building management and professional property management services for residential and commercial properties. We take the hassle out of ownership with efficient rent collection, maintenance, compliance, and more—maximizing your property's value and performance.">
    <meta name="keywords" content="HMS, best property management companies, professional property management, building management, property services">
    <!-- SEO & Social Media -->
    <meta name="robots" content="index, follow">
    <meta name="author" content="HMS Team">

    <meta name="description" content="HMS offers expert building and property management services for residential and commercial properties. From rent collection to maintenance and legal compliance, we ensure your property performs at its best.">
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
