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

        .carousel-fixed-size {
            max-width: 100%;
            max-height: 400px;
            min-height: 240px;
            height: 100%;
        }

        .carousel-fixed-size img {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        #unit-details p {
            color: var(--main-text-color);
            opacity: 0.7;
        }
        #unit-details p strong{
            color: var(--main-text-color);
        }
         #unit-details p .text-muted{
             color: var(--main-text-color);
         }

        #contract-details-card h4 {
            color: var(--main-text-color);
        }

         #contract-details-card p {
             color: var(--main-text-color);
         }

        #contract-details-card p i{
            color: var(--input-icon-color) !important;
            margin-right: 5px;
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
    <x-Owner.side-navbar :openSections="['Buildings', 'Units']"/>
    <x-error-success-model />

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row d-flex align-items-stretch">
                    <!-- Left Side: Details -->
                    <div class="col-md-6 d-flex mb-2 order-last order-md-first" id="unit-details">
                        <div class="card p-3 flex-fill shadow-sm position-relative">
                            <a href="{{ route('owner.units.edit', $unit->id) }}" style="top: 70px; right: 40px;" class="text-warning hidden Owner-Unit-Edit-Button position-absolute"  data-bs-toggle="tooltip" data-bs-placement="right" title="Edit">
                                <i class="fa fa-pencil mx-2" style="font-size: 20px;"></i>
                            </a>
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="apartment-title mb-0">{{ $unit->unit_name }}</h4>
                                <button class="btn btn-primary text-white py-1">
                                    <svg width="16" height="16" class="fw-bold" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 476.998 511.874">
                                        <path fill="currentColor" d="M416.628 111.415c0 17.381-.412 35.18-1.049 53.158 37.021-41.553 47.383-77.508 42.064-103.582-3.776-18.508-15.719-31.95-31.433-38.441-16.122-6.661-36.29-6.137-55.993 3.502-22.866 11.185-44.943 34.454-59.774 72.713l90.141-3.395c8.553-.307 15.737 6.377 16.044 14.93.014.374.013.746 0 1.115zm-257.48 149.554l6.113 6.113 18.929-18.929 23.594 23.594c7.402 7.402 11.213 15.062 11.403 22.943.155 6.494-2.506 13.081-7.285 19.033l8.516 8.515c.295.295.277.793-.009 1.079l-7.635 7.635c-.286.286-.799.29-1.08.009l-8.808-8.809c-7.88 5.51-16.483 7.358-25.119 4.857l-17.086 42.742-.705.705-15.902-15.902 15.679-37.694-7.35-7.35-26.687 26.686-14.785-14.785 45.607-45.606-6.113-6.113a.777.777 0 01.01-1.078l7.635-7.636a.764.764 0 011.078-.009zm38.383 123.506c1.001-1 1.437-2.396 1.325-4.173-.113-1.802-1.099-5.045-2.931-9.753-2.863-6.779-4.159-12.108-3.862-16.012.281-3.891 1.988-7.4 5.102-10.514 3.905-3.906 8.558-5.708 13.925-5.442 5.371.269 10.723 2.387 15.374 7.038 4.906 4.906 7.208 10.44 7.617 15.924.423 5.468-1.509 10.359-5.765 14.616l-11.685-11.684c3.637-3.637 3.932-6.977.901-10.008-1.24-1.241-2.635-1.876-4.213-1.904-1.565-.014-3.032.663-4.413 2.045-.987.987-1.424 2.297-1.31 3.932.126 1.65 1.114 4.807 3.002 9.486 3.002 6.526 4.383 11.855 4.199 15.985-.182 4.13-2.015 7.907-5.454 11.346-4.002 4.003-8.696 5.708-14.124 5.131-5.427-.578-10.57-3.298-15.448-8.175-3.27-3.27-5.525-6.794-6.752-10.585-1.226-3.793-1.396-7.542-.535-11.25.873-3.692 2.636-6.864 5.272-9.5l11.107 11.107c-1.974 2.087-3.045 4.09-3.157 6.005-.141 1.918.817 3.891 2.861 5.935 3.129 3.129 6.131 3.284 8.964.45zm-17.485-102.608l16.725 16.724c3.072-5.217 2.8-10.328-1.506-14.634l-8.655-8.655-6.564 6.565zm7.93 25.375l-16.653-16.653-6.556 6.556 8.398 8.399c2.954 2.953 6.147 4.19 9.583 3.709 1.645-.239 3.62-1.14 5.228-2.011zm226.491-115.326c-1.494 33.127-3.424 66.452-4.642 98.514a15.44 15.44 0 01-4.53 10.4l.025.025-196.436 196.436c-9.721 9.722-22.522 14.583-35.313 14.583-12.788.001-25.591-4.861-35.313-14.583L14.583 373.617C4.861 363.895 0 351.092 0 338.304c.001-12.789 4.863-25.592 14.584-35.312l196.368-196.369a15.485 15.485 0 0111.419-4.542l68.61-2.584c16.601-47.392 43.389-76.069 71.278-89.71 24.579-12.024 50.13-12.516 70.857-3.953 21.136 8.731 37.186 26.758 42.244 51.554 7.008 34.353-7.594 81.832-60.893 134.528zm-138.018-25.561c2.052-18.817 5.331-35.843 9.607-51.164l-64.121 2.415L25.567 313.974c-13.382 13.382-13.382 35.279-.001 48.66l123.675 123.675c13.382 13.381 35.282 13.378 48.66 0l196.437-196.437 6.803-179.014-96.109 3.619c-4.043 13.295-7.282 28.118-9.517 44.557a50.242 50.242 0 018.017-.645c12.784-.001 25.57 4.875 35.323 14.629 9.754 9.753 14.631 22.54 14.63 35.324.001 12.785-4.876 25.572-14.63 35.325-9.753 9.753-22.539 14.63-35.324 14.629-12.784.001-25.571-4.876-35.324-14.629-9.754-9.754-14.63-22.539-14.629-35.324-.001-12.784 4.876-25.571 14.63-35.324a50.057 50.057 0 018.241-6.664z"/>
                                    </svg>
                                    {{ $unit->sale_or_rent }}
                                </button>
                            </div>
                            <p class=" mt-2 mb-1"><i class='bx bx-building-house'></i> {{ $unit->building->name }}</p>
                            <p class="mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M6.5 20v-3.5q0-.425.288-.712T7.5 15.5H11V12q0-.425.288-.712T12 11h3.5V7.5q0-.425.288-.712T16.5 6.5H20V4q0-.425.288-.712T21 3t.713.288T22 4v3.5q0 .425-.288.713T21 8.5h-3.5V12q0 .425-.288.713T16.5 13H13v3.5q0 .425-.288.713T12 17.5H8.5V21q0 .425-.288.713T7.5 22H4q-.425 0-.712-.288T3 21t.288-.712T4 20z"/></svg>
                                    {{ $unit->level->level_name }}</p>
                            <p class="">
                                <svg width="15" height="15" viewBox="0 0 17 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.173 14.8819L13.053 16.0558C12.2275 16.9144 11.1564 18.0184 9.83928 19.3679C9.0163 20.2113 7.71058 20.2112 6.88769 19.3677L3.59355 15.9718C3.17955 15.541 2.83301 15.1777 2.55386 14.8819C-0.654672 11.4815 -0.654672 5.9683 2.55386 2.56789C5.76239 -0.832524 10.9645 -0.832524 14.173 2.56789C17.3815 5.9683 17.3815 11.4815 14.173 14.8819ZM10.7226 8.9996C10.7226 7.61875 9.66633 6.49936 8.36344 6.49936C7.06056 6.49936 6.0043 7.61875 6.0043 8.9996C6.0043 10.3804 7.06056 11.4998 8.36344 11.4998C9.66633 11.4998 10.7226 10.3804 10.7226 8.9996Z" fill="red"/>
                                </svg>
                                {{ $unit->building->address ? $unit->building->address->location . ', ' .  $unit->building->address->city . ', ' .  $unit->building->address->province . ', ' .  $unit->building->address->country : 'No Address'}}
                            </p>
                            <p>
                                <strong>Organization:</strong> {{ $unit->organization->name }}
                                <img src="{{ asset($unit->organization->pictures->first()->file_path) }}" alt="Etihad Town Logo" style="height: 20px; vertical-align: middle; margin-left: 5px;">
                            </p>


                            <p class="d-flex justify-content-between align-items-center">
                                <span class=""><strong>Status:</strong> {{ $unit->status }}</span>
                                <span class=""><strong>Availability:</strong> {{ $unit->availability_status }}</span>
                            </p>

                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary w-100"><i class='bx bxs-purchase-tag'></i> Rs {{ $unit->price }}</button>
                                <button class="btn btn-secondary w-100"> <i class='bx bx-building-house'></i> {{ $unit->unit_type }}</button>
                            </div>
                            <p class="mt-3"><strong>Description:</strong> {{ $unit->description }}</p>
                        </div>
                    </div>

                    <!-- Right Side: Image -->
                    <div class="col-md-6 d-flex mb-2 order-first order-md-last">
                        <div class="card flex-fill p-0 overflow-hidden shadow-sm">
                            <div id="apartmentCarousel" class="carousel slide carousel-fixed-size" data-bs-ride="carousel">

                                <!-- Indicators -->
                                <div class="carousel-indicators">
                                    @foreach($unit->pictures as $index => $picture)
                                        <button type="button" data-bs-target="#apartmentCarousel" data-bs-slide-to="{{ $index }}"
                                                class="{{ $index == 0 ? 'active' : '' }}"
                                                aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                                                aria-label="Slide {{ $index + 1 }}">
                                        </button>
                                    @endforeach
                                </div>

                                <!-- Carousel Items -->
                                <div class="carousel-inner h-100">
                                    @foreach($unit->pictures as $index => $picture)
                                        <div class="carousel-item h-100 {{ $index == 0 ? 'active' : '' }}">
                                            <img src="{{ asset($picture->file_path) }}" class="d-block img-fluid" alt="Apartment Image {{ $index + 1 }}">
                                        </div>
                                    @endforeach
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

                <!-- Active Contract -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card shadow-sm p-4 position-relative" id="contract-details-card">
                            <h4 class="mb-3">Active Contract</h4>
                            @if($unit->availability_status === "Available")
                                <p class="text-center">No Active Contract</p>
                            @else
                                @if($activeContract->type === "Rented")
                                <button class="btn btn-danger position-absolute" style="top: 20px; right: 20px;" title="Delete Contract">
                                    <i class="bx bx-edit"></i>
                                </button>
                                @endif

                            <div>
                                <h5 class="text-primary"><i class="bi bi-key-fill"></i> {{ $activeContract->type . ' ' . $unit->unit_type }}</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p><i class="bx bxs-user"></i> <strong>User Name:</strong> {{ $activeContract->user->name }}</p>
                                        <p><i class="bx bxs-envelope"></i> <strong>Email:</strong> {{ $activeContract->user->email }}</p>
                                        <p><i class="bx bxs-id-card"></i> <strong>CNIC:</strong> {{ $activeContract->user->cnic }}</p>
                                        <p><i class="bx bxs-mobile"></i> <strong>Phone Number:</strong> {{ $activeContract->user->phone_no }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="bx bxs-location-plus"></i> <strong>Address:</strong> {{ $activeContract->user->address ? $activeContract->user->address->location . ', ' .  $activeContract->user->address->city . ', ' .  $activeContract->user->address->province . ', ' .  $activeContract->user->address->country : 'No Address'}}</p>
                                        @if($activeContract->type === "Rented")
                                        <p><i class="bx bxs-calendar"></i> <strong>Rent Start Date:</strong> {{ $activeContract->rent_start_date }}</p>
                                        <p><i class="bx bxs-calendar"></i> <strong>Rent End Date:</strong> {{ $activeContract->rent_end_date }}</p>
                                        @elseif($activeContract->type === "Sold")
                                            <p><i class="bx bxs-calendar-alt"></i> <strong>Purchase Date:</strong> {{ $activeContract->purchase_date }}</p>
                                        @endif
                                        <p><i class="bx bxs-purchase-tag"></i> <strong>Contract Price:</strong> Rs {{ $activeContract->price }} PKR</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Past Contract -->
                @if($pastContract)
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card shadow-sm p-4 position-relative" id="contract-details-card">
                            <h4 class="mb-3">Past Contract</h4>
                            <div>
                                <h5 class="text-primary"><i class="bi bi-key-fill"></i> {{ $pastContract->type . ' ' . $unit->unit_type }}</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p><i class="bx bxs-user"></i> <strong>User Name:</strong> {{ $pastContract->user->name }}</p>
                                        <p><i class="bx bxs-envelope"></i> <strong>Email:</strong> {{ $pastContract->user->email }}</p>
                                        <p><i class="bx bxs-id-card"></i> <strong>CNIC:</strong> {{ $pastContract->user->cnic }}</p>
                                        <p><i class="bx bxs-mobile"></i> <strong>Phone Number:</strong> {{ $pastContract->user->phone_no }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="bx bxs-location-plus"></i> <strong>Address:</strong> {{ $pastContract->user->address ? $activeContract->user->address->location . ', ' .  $activeContract->user->address->city . ', ' .  $activeContract->user->address->province . ', ' .  $activeContract->user->address->country : 'No Address'}}</p>
                                        @if($pastContract->type === "Rented")
                                            <p><i class="bx bxs-calendar"></i> <strong>Rent Start Date:</strong> {{ $pastContract->rent_start_date }}</p>
                                            <p><i class="bx bxs-calendar"></i> <strong>Rent End Date:</strong> {{ $pastContract->rent_end_date }}</p>
                                        @elseif($pastContract->type === "Sold")
                                            <p><i class="bx bxs-calendar-alt"></i> <strong>Purchase Date:</strong> {{ $pastContract->purchase_date }}</p>
                                        @endif
                                        <p><i class="bx bxs-purchase-tag"></i> <strong>Contract Price:</strong> Rs {{ $pastContract->price }} PKR</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </section>



    </div>

@endsection

@push('scripts')

@endpush

