@extends('layouts.landing')

@section('title', 'About Us - HMS Property Management')

@push('styles')
    <style>
        .team-member:hover .team-overlay {
            opacity: 1;
            transform: translateY(0);
        }
        .team-overlay {
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        }
        .values-card {
            transition: all 0.3s ease;
        }
        .values-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <x-Landing.navbar />

    <!-- Hero Section -->
    <div class="relative bg-gray-900">
        <div class="absolute inset-0 w-full h-full overflow-hidden">
            <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover opacity-40"
                   poster="{{ asset('videos/bg-video-img.jpeg') }}">
                <source src="{{ asset('videos/bg-video.mp4') }}" type="video/mp4">
            </video>
        </div>

        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                About Heights Management System
            </h1>
            <p class="mt-6 text-xl text-gray-300 max-w-3xl mx-auto">
                Transforming property management through innovation, expertise, and exceptional service.
            </p>
        </div>
    </div>

    <!-- Our Story Section -->
    <div class="py-16 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="relative lg:order-1">
                    <div class="relative rounded-lg overflow-hidden">
                        <img class="w-full h-auto rounded-lg shadow-xl"
                             src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                             alt="Our office">
                        <div class="absolute inset-0] mix-blend-multiply rounded-lg" style="background-color: #008CFF;"></div>
                    </div>
                </div>
                <div class="mt-12 lg:mt-0">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                        Our Story
                    </h2>
                    <div class="mt-6 text-lg text-gray-600 space-y-6">
                        <p>
                            Founded in 2015, Heights Management System began with a simple vision: to revolutionize property management through technology and personalized service.
                        </p>
                        <p>
                            We recognized early on that property owners needed more than just basic services—they needed a partner who could anticipate challenges and deliver exceptional results.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mission Section -->
    <div class="" style="background-color: #008CFF;">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    Our Mission & Vision
                </h2>
                <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-2">
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <div class="" style="color: #008CFF;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-medium text-gray-900">Mission</h3>
                        <p class="mt-4 text-base text-gray-600">
                            To empower property owners with innovative management solutions that maximize returns and create exceptional living experiences.
                        </p>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <div class="" style="color: #008CFF;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-medium text-gray-900">Vision</h3>
                        <p class="mt-4 text-base text-gray-600">
                            To become the most trusted and technologically advanced property management partner in the region.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Values Section -->
    <div class="py-16 bg-gray-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                    Our Core Values
                </h2>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="values-card bg-white p-8 rounded-lg shadow-md text-center">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-100" style="color: #008CFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Innovation</h3>
                    <p class="mt-2 text-base text-gray-600">
                        We constantly seek better ways to serve our clients.
                    </p>
                </div>

                <div class="values-card bg-white p-8 rounded-lg shadow-md text-center">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-100" style="color: #008CFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Excellence</h3>
                    <p class="mt-2 text-base text-gray-600">
                        We deliver quality service that exceeds expectations.
                    </p>
                </div>

                <div class="values-card bg-white p-8 rounded-lg shadow-md text-center">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-100" style="color: #008CFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Integrity</h3>
                    <p class="mt-2 text-base text-gray-600">
                        We conduct business with honesty and transparency.
                    </p>
                </div>

                <div class="values-card bg-white p-8 rounded-lg shadow-md text-center">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-100" style="color: #008CFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Accountability</h3>
                    <p class="mt-2 text-base text-gray-600">
                        We take ownership of our commitments.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="" style="background-color: #008CFF;">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    Trusted by property owners
                </h2>
            </div>
            <div class="mt-10 text-center sm:max-w-3xl sm:mx-auto sm:grid sm:grid-cols-3 sm:gap-8">
                <div>
                    <p class="text-5xl font-extrabold text-white">500+</p>
                    <p class="mt-2 text-base font-medium text-blue-200">Properties Managed</p>
                </div>
                <div class="mt-10 sm:mt-0">
                    <p class="text-5xl font-extrabold text-white">98%</p>
                    <p class="mt-2 text-base font-medium text-blue-200">Client Retention</p>
                </div>
                <div class="mt-10 sm:mt-0">
                    <p class="text-5xl font-extrabold text-white">24/7</p>
                    <p class="mt-2 text-base font-medium text-blue-200">Support</p>
                </div>
            </div>
        </div>
    </div>

    <x-Landing.footer />
@endsection
