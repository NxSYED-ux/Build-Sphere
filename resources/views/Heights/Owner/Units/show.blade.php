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
            color: var(--sidenavbar-text-color);
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
                        <div class="card p-4 pb-1 flex-fill shadow-sm position-relative" style="border-radius: 12px;">
                            <!-- Edit Button (Top Right) - Moved to be separate from the button group -->
                            <a href="{{ route('owner.units.edit', $unit->id) }}"
                               class="text-warning hidden Owner-Unit-Edit-Button position-absolute"
                               style="top: 80px; right: 30px;"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Unit">
                                <i class="fas fa-edit fs-5"></i>
                            </a>

                            <!-- Unit Title and Action Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="apartment-title mb-0">
                                    <i class="fas fa-home me-2"></i>
                                    {{ $unit->unit_name }}
                                </h4>

                                <div class="d-flex gap-2 align-items-center">
                                    @if (!in_array($unit->unit_type, ['Restaurant', 'Gym', 'Other']))
                                        <!-- Smaller Sale/Rent Tag -->
                                        <span class="btn btn-primary py-1 px-2 d-flex align-items-center"
                                              style="font-size: 0.8rem; border-radius: 6px; color: #fff !important;">
                                            <svg width="14" height="14" class="me-1" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 476.998 511.874">
                                                <path d="M416.628 111.415c0 17.381-.412 35.18-1.049 53.158 37.021-41.553 47.383-77.508 42.064-103.582-3.776-18.508-15.719-31.95-31.433-38.441-16.122-6.661-36.29-6.137-55.993 3.502-22.866 11.185-44.943 34.454-59.774 72.713l90.141-3.395c8.553-.307 15.737 6.377 16.044 14.93.014.374.013.746 0 1.115zm-257.48 149.554l6.113 6.113 18.929-18.929 23.594 23.594c7.402 7.402 11.213 15.062 11.403 22.943.155 6.494-2.506 13.081-7.285 19.033l8.516 8.515c.295.295.277.793-.009 1.079l-7.635 7.635c-.286.286-.799.29-1.08.009l-8.808-8.809c-7.88 5.51-16.483 7.358-25.119 4.857l-17.086 42.742-.705.705-15.902-15.902 15.679-37.694-7.35-7.35-26.687 26.686-14.785-14.785 45.607-45.606-6.113-6.113a.777.777 0 01.01-1.078l7.635-7.636a.764.764 0 011.078-.009zm38.383 123.506c1.001-1 1.437-2.396 1.325-4.173-.113-1.802-1.099-5.045-2.931-9.753-2.863-6.779-4.159-12.108-3.862-16.012.281-3.891 1.988-7.4 5.102-10.514 3.905-3.906 8.558-5.708 13.925-5.442 5.371.269 10.723 2.387 15.374 7.038 4.906 4.906 7.208 10.44 7.617 15.924.423 5.468-1.509 10.359-5.765 14.616l-11.685-11.684c3.637-3.637 3.932-6.977.901-10.008-1.24-1.241-2.635-1.876-4.213-1.904-1.565-.014-3.032.663-4.413 2.045-.987.987-1.424 2.297-1.31 3.932.126 1.65 1.114 4.807 3.002 9.486 3.002 6.526 4.383 11.855 4.199 15.985-.182 4.13-2.015 7.907-5.454 11.346-4.002 4.003-8.696 5.708-14.124 5.131-5.427-.578-10.57-3.298-15.448-8.175-3.27-3.27-5.525-6.794-6.752-10.585-1.226-3.793-1.396-7.542-.535-11.25.873-3.692 2.636-6.864 5.272-9.5l11.107 11.107c-1.974 2.087-3.045 4.09-3.157 6.005-.141 1.918.817 3.891 2.861 5.935 3.129 3.129 6.131 3.284 8.964.45zm-17.485-102.608l16.725 16.724c3.072-5.217 2.8-10.328-1.506-14.634l-8.655-8.655-6.564 6.565zm7.93 25.375l-16.653-16.653-6.556 6.556 8.398 8.399c2.954 2.953 6.147 4.19 9.583 3.709 1.645-.239 3.62-1.14 5.228-2.011zm226.491-115.326c-1.494 33.127-3.424 66.452-4.642 98.514a15.44 15.44 0 01-4.53 10.4l.025.025-196.436 196.436c-9.721 9.722-22.522 14.583-35.313 14.583-12.788.001-25.591-4.861-35.313-14.583L14.583 373.617C4.861 363.895 0 351.092 0 338.304c.001-12.789 4.863-25.592 14.584-35.312l196.368-196.369a15.485 15.485 0 0111.419-4.542l68.61-2.584c16.601-47.392 43.389-76.069 71.278-89.71 24.579-12.024 50.13-12.516 70.857-3.953 21.136 8.731 37.186 26.758 42.244 51.554 7.008 34.353-7.594 81.832-60.893 134.528zm-138.018-25.561c2.052-18.817 5.331-35.843 9.607-51.164l-64.121 2.415L25.567 313.974c-13.382 13.382-13.382 35.279-.001 48.66l123.675 123.675c13.382 13.381 35.282 13.378 48.66 0l196.437-196.437 6.803-179.014-96.109 3.619c-4.043 13.295-7.282 28.118-9.517 44.557a50.242 50.242 0 018.017-.645c12.784-.001 25.57 4.875 35.323 14.629 9.754 9.753 14.631 22.54 14.63 35.324.001 12.785-4.876 25.572-14.63 35.325-9.753 9.753-22.539 14.63-35.324 14.629-12.784.001-25.571-4.876-35.324-14.629-9.754-9.754-14.63-22.539-14.629-35.324-.001-12.784 4.876-25.571 14.63-35.324a50.057 50.057 0 018.241-6.664z"/>
                                            </svg>
                                            {{ $unit->sale_or_rent }}
                                        </span>
                                    @endif

                                    <!-- Assign Unit Button -->
                                    @if (!in_array($unit->unit_type, ['Restaurant', 'Gym', 'Other']) && $unit->availability_status === 'Available')
                                        <button class="btn btn-success py-1 px-2 d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Assign Unit to User"
                                                style="border: none; border-radius: 6px; font-size: 0.8rem;"
                                                onclick="window.location.href='{{ route('owner.assignunits.index', ['unit_id' => $unit->id]) }}'">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Assign
                                        </button>
                                    @endif

                                </div>
                            </div>

                            <!-- Rest of the content remains the same -->
                            <!-- Unit Details -->
                            <div class="unit-details mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class='bx bx-building-house me-2 fs-5' style="color: #7f8c8d;"></i>
                                    <span class="text-muted">{{ $unit->building->name }}</span>
                                </div>

                                <div class="d-flex align-items-center mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" class="me-2" fill="#7f8c8d">
                                        <path d="M6.5 20v-3.5q0-.425.288-.712T7.5 15.5H11V12q0-.425.288-.712T12 11h3.5V7.5q0-.425.288-.712T16.5 6.5H20V4q0-.425.288-.712T21 3t.713.288T22 4v3.5q0 .425-.288.713T21 8.5h-3.5V12q0 .425-.288.713T16.5 13H13v3.5q0 .425-.288.713T12 17.5H8.5V21q0 .425-.288.713T7.5 22H4q-.425 0-.712-.288T3 21t.288-.712T4 20z"/>
                                    </svg>
                                    <span class="text-muted">{{ $unit->level->level_name }}</span>
                                </div>

                                <div class="d-flex ">
                                    <svg width="15" height="15" viewBox="0 0 17 20" fill="#e74c3c" class="me-2 mt-1" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.173 14.8819L13.053 16.0558C12.2275 16.9144 11.1564 18.0184 9.83928 19.3679C9.0163 20.2113 7.71058 20.2112 6.88769 19.3677L3.59355 15.9718C3.17955 15.541 2.83301 15.1777 2.55386 14.8819C-0.654672 11.4815 -0.654672 5.9683 2.55386 2.56789C5.76239 -0.832524 10.9645 -0.832524 14.173 2.56789C17.3815 5.9683 17.3815 11.4815 14.173 14.8819ZM10.7226 8.9996C10.7226 7.61875 9.66633 6.49936 8.36344 6.49936C7.06056 6.49936 6.0043 7.61875 6.0043 8.9996C6.0043 10.3804 7.06056 11.4998 8.36344 11.4998C9.66633 11.4998 10.7226 10.3804 10.7226 8.9996Z"/>
                                    </svg>
                                    <span class="text-muted">
                                        {{ $unit->building->address ? $unit->building->address->location . ', ' .  $unit->building->address->city . ', ' .  $unit->building->address->province . ', ' .  $unit->building->address->country : 'No Address'}}
                                    </span>
                                </div>
                            </div>

                            <!-- Status and Availability -->
                            <div class="status-section mb-2 py-1 px-3" style="background-color: var(--main-background-color2); border-radius: 8px;">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <span class="text small">Status</span>
                                            <span class="fw-bold">{{ $unit->status }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <span class="text small">Availability</span>
                                            <span class="fw-bold">{{ $unit->availability_status }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Price and Type Buttons -->
                            <div class="d-flex gap-3 mb-4">
                                @if (!in_array($unit->unit_type, ['Restaurant', 'Gym', 'Other']))
                                    <button class="btn btn-primary flex-grow-1 py-2 d-flex align-items-center justify-content-center"
                                            style="border-radius: 8px; border: none;">
                                        <i class='bx bxs-purchase-tag me-2 fs-5'></i>
                                        Rs {{ number_format($unit->price) }}
                                    </button>
                                @endif
                                <button class="btn btn-secondary flex-grow-1 py-2 d-flex align-items-center justify-content-center"
                                        style="border-radius: 8px;  border: none;">
                                    <i class='bx bx-building-house me-2 fs-5'></i>
                                    {{ $unit->unit_type }}
                                </button>
                            </div>

                            <!-- Description -->
                            <div class="description-section">
                                <h6 class="fw-bold mb-2" style="color: var(--sidenavbar-text-color);">Description</h6>
                                <p class="text" style="line-height: 1.6;">
                                    {{ $unit->description ?: 'No description provided' }}
                                </p>
                            </div>
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
                            @if($unit->availability_status === "Available")
                                <div class="">
                                    <h4 class="mb-3">Active Contract</h4>
                                    <p class="text-center mb-0">No Active Contract</p>
                                </div>
                            @else
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-3">Active Contract</h4>
                                    @if($activeContract->type === "Rented")
                                        <div class="btn-group gap-2" role="group">
                                            <button class="btn btn-warning contract-edit-btn rounded" style="color: #fff !important;" data-contract-id="{{ $activeContract->id }}" title="Edit Contract">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                            <form action="#" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger contract-delete-btn" data-contract-id="{{ $activeContract->id }}" title="Delete Contract">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

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
                                                <p><i class="bx bxs-calendar"></i> <strong>Rent Start Date:</strong> {{ $activeContract->subscription->created_at }}</p>
                                                <p><i class="bx bxs-calendar"></i> <strong>Rent End Date:</strong> {{ $activeContract->subscription->ends_at }}</p>
                                                <p><i class="bx bxs-purchase-tag"></i> <strong>Contract Price:</strong> Rs {{ $activeContract->subscription->price_at_subscription }} PKR</p>
                                            @elseif($activeContract->type === "Sold")
                                                <p><i class="bx bxs-calendar-alt"></i> <strong>Purchase Date:</strong> {{ $activeContract->created_at }}</p>
                                                <p><i class="bx bxs-purchase-tag"></i> <strong>Contract Price:</strong> Rs {{ $activeContract->price }} PKR</p>
                                            @endif
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
                                            <p><i class="bx bxs-calendar"></i> <strong>Rent Start Date:</strong> {{ $pastContract->subscription->created_at }}</p>
                                            <p><i class="bx bxs-calendar"></i> <strong>Rent End Date:</strong> {{ $pastContract->subscription->ends_at }}</p>
                                            <p><i class="bx bxs-purchase-tag"></i> <strong>Contract Price:</strong> Rs {{ $pastContract->subscription->price_at_subscription }} PKR</p>
                                        @elseif($pastContract->type === "Sold")
                                            <p><i class="bx bxs-calendar-alt"></i> <strong>Purchase Date:</strong> {{ $pastContract->created_at }}</p>
                                            <p><i class="bx bxs-purchase-tag"></i> <strong>Contract Price:</strong> Rs {{ $pastContract->price }} PKR</p>
                                        @endif
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
    <script>
        console.log(@json($activeContract));
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle contract edit button clicks
            document.querySelectorAll('.contract-edit-btn').forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();

                    // Get the contract ID
                    const contractId = this.getAttribute('data-contract-id') ||
                        this.closest('[data-contract-id]')?.getAttribute('data-contract-id');

                    if (!contractId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Contract ID not found',
                            confirmButtonColor: '#3085d6',
                            background: 'var(--body-background-color)',
                            color: 'var(--sidenavbar-text-color)',
                        });
                        return;
                    }

                    try {

                        // Make the fetch request
                        const response = await fetch(`{{ route('owner.property.users.contract.edit', ':id') }}`.replace(':id', contractId), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        // Close loading SweetAlert
                        Swal.close();

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.error) {
                            throw new Error(data.error);
                        }

                        // Create and show the edit modal
                        showEditContractModal(data.contract);

                    } catch (error) {
                        console.error('Error fetching contract:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to load contract details',
                            confirmButtonColor: '#3085d6',
                            background: 'var(--body-background-color)',
                            color: 'var(--sidenavbar-text-color)',
                        });
                    }
                });
            });

            // Function to show the edit contract modal
            function showEditContractModal(contract) {
                // Create modal HTML
                const modalHtml = `
            <div class="modal fade" id="editContractModal" tabindex="-1" aria-labelledby="editContractModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content" style="background-color: var(--body-card-bg);">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editContractModalLabel" style="color: var(--sidenavbar-text-color)">Edit Contract</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editContractForm" method="POST" action="{{ route('owner.property.users.contract.update') }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="contract_id" value="${contract.id}">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="billingCycle" class="form-label">Billing Cycle (months)</label>
                                        <input type="number" class="form-control" id="billingCycle" name="billing_cycle"
                                               value="${contract.billing_cycle}" min="1" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" class="form-control" id="price" name="price"
                                               value="${contract.price}" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="editContractForm" class="btn btn-primary" id="saveContractChanges">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

                // Add modal to DOM
                const modalContainer = document.createElement('div');
                modalContainer.innerHTML = modalHtml;
                document.body.appendChild(modalContainer);

                // Initialize modal
                const modal = new bootstrap.Modal(document.getElementById('editContractModal'));
                modal.show();

                // Handle form submission
                document.getElementById('editContractForm').addEventListener('submit', function(e) {
                    // Show loading state
                    const saveButton = document.getElementById('saveContractChanges');
                    saveButton.disabled = true;
                    saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                    // The form will submit normally and the page will refresh
                });

                // Clean up modal when closed
                document.getElementById('editContractModal').addEventListener('hidden.bs.modal', function() {
                    modalContainer.remove();
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.contract-delete-btn');

            deleteButtons.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    const form = btn.closest('.delete-form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush

