@extends('layouts.landing')

@section('title', 'Contact Us - HMS Property Management')

@push('styles')
    <style>
        .contact-card {
            transition: all 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        #contact-form .form-input:focus {
            border-color: var(--color-blue);
            box-shadow: 0 0 0 3px rgba(var(--color-blue), 0.2);
        }
        .map-container {
            height: 100%;
            min-height: 300px;
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
                Contact Heights Management System
            </h1>
            <p class="mt-6 text-xl text-gray-300 max-w-3xl mx-auto">
                We're here to help with all your property management needs.
            </p>
        </div>
    </div>

    <!-- Contact Info Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="contact-card bg-white rounded-lg shadow-lg p-8 text-center">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-100" style="color: #008CFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Phone</h3>
                    <p class="mt-2 text-base text-gray-600">
                        (123) 456-7890
                    </p>
                </div>

                <div class="contact-card bg-white rounded-lg shadow-lg p-8 text-center">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-100" style="color: #008CFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Email</h3>
                    <p class="mt-2 text-base text-gray-600">
                        info@hms.com
                    </p>
                </div>

                <div class="contact-card bg-white rounded-lg shadow-lg p-8 text-center">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-100" style="color: #008CFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-medium text-gray-900">Office</h3>
                    <p class="mt-2 text-base text-gray-600">
                        123 Property Lane, Suite 500
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form and Map Section -->
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8">
                <div class="mb-12 lg:mb-0">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                        Send us a message
                    </h2>

                    <form id="contact-form" class="mt-8 space-y-6" action="#" method="POST">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <div class="mt-1">
                                <input id="name" name="name" type="text" required
                                       class="form-input py-3 px-4 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1">
                                <input id="email" name="email" type="email" required
                                       class="form-input py-3 px-4 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <div class="mt-1">
                                <textarea id="message" name="message" rows="4"
                                          class="form-input py-3 px-4 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                    class="w-full flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:bg-blue-700" style="background-color: #008CFF;">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                <div class="relative">
                    <div class="bg-white p-6 rounded-lg shadow-lg h-full">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Our Headquarters</h3>
                        <div class="map-container rounded-lg overflow-hidden">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215256027566!2d-73.98784492423996!3d40.74844097138961!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9b3117469%3A0xd134e199a405a163!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus"
                                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="" style="background-color: #008CFF;">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8 lg:py-20 text-center">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Still have questions?
            </h2>
            <div class="mt-8 flex justify-center">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md bg-white hover:bg-blue-50" style="color: #008CFF;">
                        Contact Our Team
                    </a>
                </div>
            </div>
        </div>
    </div>

    <x-Landing.footer />
@endsection
