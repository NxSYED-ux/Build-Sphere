@extends('layouts.app')

@section('title', 'Unit Details')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .badge-sale {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-outline-secondary {
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        .apartment-title {
            color: #008c72;
            font-weight: bold;
        }
        .location-icon {
            color: red;
        }

        .carousel,
        .carousel-inner,
        .carousel-item {
            height: 100%;
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Unit Details']
        ]"
    />
    <!--  -->
    <x-Owner.side-navbar :openSections="['Buildings', 'Building']"/>
    <x-error-success-model />

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row d-flex align-items-stretch">
                    <!-- Left Side: Details -->
                    <div class="col-md-6 d-flex mb-2 order-last order-md-first">
                        <div class="card p-3 flex-fill shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="apartment-title mb-0">Apartment 07</h4>
                                <button class="btn btn-primary text-white py-1">
                                    <svg width="16" height="16" class="fw-bold" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 476.998 511.874">
                                        <path fill="currentColor" d="M416.628 111.415c0 17.381-.412 35.18-1.049 53.158 37.021-41.553 47.383-77.508 42.064-103.582-3.776-18.508-15.719-31.95-31.433-38.441-16.122-6.661-36.29-6.137-55.993 3.502-22.866 11.185-44.943 34.454-59.774 72.713l90.141-3.395c8.553-.307 15.737 6.377 16.044 14.93.014.374.013.746 0 1.115zm-257.48 149.554l6.113 6.113 18.929-18.929 23.594 23.594c7.402 7.402 11.213 15.062 11.403 22.943.155 6.494-2.506 13.081-7.285 19.033l8.516 8.515c.295.295.277.793-.009 1.079l-7.635 7.635c-.286.286-.799.29-1.08.009l-8.808-8.809c-7.88 5.51-16.483 7.358-25.119 4.857l-17.086 42.742-.705.705-15.902-15.902 15.679-37.694-7.35-7.35-26.687 26.686-14.785-14.785 45.607-45.606-6.113-6.113a.777.777 0 01.01-1.078l7.635-7.636a.764.764 0 011.078-.009zm38.383 123.506c1.001-1 1.437-2.396 1.325-4.173-.113-1.802-1.099-5.045-2.931-9.753-2.863-6.779-4.159-12.108-3.862-16.012.281-3.891 1.988-7.4 5.102-10.514 3.905-3.906 8.558-5.708 13.925-5.442 5.371.269 10.723 2.387 15.374 7.038 4.906 4.906 7.208 10.44 7.617 15.924.423 5.468-1.509 10.359-5.765 14.616l-11.685-11.684c3.637-3.637 3.932-6.977.901-10.008-1.24-1.241-2.635-1.876-4.213-1.904-1.565-.014-3.032.663-4.413 2.045-.987.987-1.424 2.297-1.31 3.932.126 1.65 1.114 4.807 3.002 9.486 3.002 6.526 4.383 11.855 4.199 15.985-.182 4.13-2.015 7.907-5.454 11.346-4.002 4.003-8.696 5.708-14.124 5.131-5.427-.578-10.57-3.298-15.448-8.175-3.27-3.27-5.525-6.794-6.752-10.585-1.226-3.793-1.396-7.542-.535-11.25.873-3.692 2.636-6.864 5.272-9.5l11.107 11.107c-1.974 2.087-3.045 4.09-3.157 6.005-.141 1.918.817 3.891 2.861 5.935 3.129 3.129 6.131 3.284 8.964.45zm-17.485-102.608l16.725 16.724c3.072-5.217 2.8-10.328-1.506-14.634l-8.655-8.655-6.564 6.565zm7.93 25.375l-16.653-16.653-6.556 6.556 8.398 8.399c2.954 2.953 6.147 4.19 9.583 3.709 1.645-.239 3.62-1.14 5.228-2.011zm226.491-115.326c-1.494 33.127-3.424 66.452-4.642 98.514a15.44 15.44 0 01-4.53 10.4l.025.025-196.436 196.436c-9.721 9.722-22.522 14.583-35.313 14.583-12.788.001-25.591-4.861-35.313-14.583L14.583 373.617C4.861 363.895 0 351.092 0 338.304c.001-12.789 4.863-25.592 14.584-35.312l196.368-196.369a15.485 15.485 0 0111.419-4.542l68.61-2.584c16.601-47.392 43.389-76.069 71.278-89.71 24.579-12.024 50.13-12.516 70.857-3.953 21.136 8.731 37.186 26.758 42.244 51.554 7.008 34.353-7.594 81.832-60.893 134.528zm-138.018-25.561c2.052-18.817 5.331-35.843 9.607-51.164l-64.121 2.415L25.567 313.974c-13.382 13.382-13.382 35.279-.001 48.66l123.675 123.675c13.382 13.381 35.282 13.378 48.66 0l196.437-196.437 6.803-179.014-96.109 3.619c-4.043 13.295-7.282 28.118-9.517 44.557a50.242 50.242 0 018.017-.645c12.784-.001 25.57 4.875 35.323 14.629 9.754 9.753 14.631 22.54 14.63 35.324.001 12.785-4.876 25.572-14.63 35.325-9.753 9.753-22.539 14.63-35.324 14.629-12.784.001-25.571-4.876-35.324-14.629-9.754-9.754-14.63-22.539-14.629-35.324-.001-12.784 4.876-25.571 14.63-35.324a50.057 50.057 0 018.241-6.664z"/>
                                    </svg>
                                    Rent
                                </button>
                            </div>
                            <p class="text-muted mt-2 mb-1"><i class='bx bx-building-house'></i> Islamabad Sky Apartments</p>
                            <p class="mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M6.5 20v-3.5q0-.425.288-.712T7.5 15.5H11V12q0-.425.288-.712T12 11h3.5V7.5q0-.425.288-.712T16.5 6.5H20V4q0-.425.288-.712T21 3t.713.288T22 4v3.5q0 .425-.288.713T21 8.5h-3.5V12q0 .425-.288.713T16.5 13H13v3.5q0 .425-.288.713T12 17.5H8.5V21q0 .425-.288.713T7.5 22H4q-.425 0-.712-.288T3 21t.288-.712T4 20z"/></svg>
                                  Ground Floor</p>
                            <p class="text-muted">
                                <svg width="15" height="15" viewBox="0 0 17 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.173 14.8819L13.053 16.0558C12.2275 16.9144 11.1564 18.0184 9.83928 19.3679C9.0163 20.2113 7.71058 20.2112 6.88769 19.3677L3.59355 15.9718C3.17955 15.541 2.83301 15.1777 2.55386 14.8819C-0.654672 11.4815 -0.654672 5.9683 2.55386 2.56789C5.76239 -0.832524 10.9645 -0.832524 14.173 2.56789C17.3815 5.9683 17.3815 11.4815 14.173 14.8819ZM10.7226 8.9996C10.7226 7.61875 9.66633 6.49936 8.36344 6.49936C7.06056 6.49936 6.0043 7.61875 6.0043 8.9996C6.0043 10.3804 7.06056 11.4998 8.36344 11.4998C9.66633 11.4998 10.7226 10.3804 10.7226 8.9996Z" fill="red"/>
                                </svg>
                                Daman-e-Koh, Islamabad, Punjab, Pakistan
                            </p>
                            <p>
                                <strong>Organization:</strong> Etihad Town
                                <img src="{{ asset('img/buildings/organization.jpeg') }}" alt="Etihad Town Logo" style="height: 20px; vertical-align: middle; margin-left: 5px;">
                            </p>


                            <p class="d-flex justify-content-between align-items-center">
                                <strong>Status:</strong> Approved
                                <span style="margin-left: 20px;"><strong>Availability:</strong> Rented</span>
                            </p>


                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary w-100"><i class='bx bxs-purchase-tag'></i> Rs 35,000 PKR</button>
                                <button class="btn btn-outline-secondary w-100"> <i class='bx bx-building-house'></i> Apartment</button>
                            </div>
                            <p class="mt-3"><strong>Description:</strong> A modern two-bedroom apartment with an open living area, stainless steel appliances, spacious bedrooms, and a private balcony with city views.</p>
                        </div>
                    </div>

                    <!-- Right Side: Image -->
                    <div class="col-md-6 d-flex mb-2 order-first order-md-last">
                        <div class="card flex-fill p-0 overflow-hidden shadow-sm">
                            <div id="apartmentCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                                <!-- Indicators -->
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#apartmentCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                    <button type="button" data-bs-target="#apartmentCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                    <button type="button" data-bs-target="#apartmentCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                </div>

                                <!-- Carousel Items -->
                                <div class="carousel-inner h-100">
                                    <div class="carousel-item active h-100">
                                        <img src="{{ asset('img/buildings/Apartment_1.jpg') }}" class="d-block w-100 h-100 img-cover" alt="Apartment Image 1">
                                    </div>
                                    <div class="carousel-item h-100">
                                        <img src="{{ asset('img/buildings/Apartment_1.jpg') }}" class="d-block w-100 h-100 img-cover" alt="Apartment Image 2">
                                    </div>
                                    <div class="carousel-item h-100">
                                        <img src="{{ asset('img/buildings/Apartment_1.jpg') }}" class="d-block w-100 h-100 img-cover" alt="Apartment Image 3">
                                    </div>
                                </div>

                                <!-- Controls -->
                                <button class="carousel-control-prev" type="button" data-bs-target="#apartmentCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#apartmentCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contract Details Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm p-4 position-relative">
                            <h4 class="mb-3">Contract Details</h4>

                            <!-- Delete Button (Top-Right Corner) -->
                            <button class="btn btn-outline-danger position-absolute top-0 end-0 mt-2 me-2" title="Delete Contract">
                                <i class="bi bi-trash"></i>
                            </button>

                            <!-- If Rented -->
                            <div id="rentedDetails" >
                                <h5 class="text-primary"><i class="bi bi-key-fill"></i> Rented Apartment</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p><i class="bi bi-person-fill"></i> <strong>User Name:</strong> John Doe</p>
                                        <p><i class="bi bi-envelope-fill"></i> <strong>Email:</strong> johndoe@example.com</p>
                                        <p><i class="bi bi-credit-card-fill"></i> <strong>CNIC:</strong> 12345-6789012-3</p>
                                        <p><i class="bi bi-phone-fill"></i> <strong>Phone Number:</strong> +92 333 1234567</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="bi bi-house-door-fill"></i> <strong>Address:</strong> Street 123, Islamabad</p>
                                        <p><i class="bi bi-calendar-check"></i> <strong>Rent Start Date:</strong> 01-Jan-2024</p>
                                        <p><i class="bi bi-calendar-x"></i> <strong>Rent End Date:</strong> 31-Dec-2024</p>
                                        <p><i class="bi bi-cash-stack"></i> <strong>Contract Price:</strong> Rs 50,000 PKR</p>
                                    </div>
                                </div>
                            </div>

                            <!-- If Sold -->
                            <div id="soldDetails" >
                                <h5 class="text-success"><i class="bi bi-cart-check-fill"></i> Sold Apartment</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p><i class="bi bi-person-fill"></i> <strong>User Name:</strong> John Doe</p>
                                        <p><i class="bi bi-envelope-fill"></i> <strong>Email:</strong> johndoe@example.com</p>
                                        <p><i class="bi bi-credit-card-fill"></i> <strong>CNIC:</strong> 12345-6789012-3</p>
                                        <p><i class="bi bi-phone-fill"></i> <strong>Phone Number:</strong> +92 333 1234567</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="bi bi-house-door-fill"></i> <strong>Address:</strong> Street 123, Islamabad</p>
                                        <p><i class="bi bi-calendar-check"></i> <strong>Purchase Date:</strong> 15-Aug-2023</p>
                                        <p><i class="bi bi-cash-stack"></i> <strong>Contract Price:</strong> Rs 7,500,000 PKR</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



    </div>

@endsection

@push('scripts')

@endpush

