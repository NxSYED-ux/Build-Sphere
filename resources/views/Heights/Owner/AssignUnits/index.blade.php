@extends('layouts.app')

@section('title', 'Assign Unit')

@push('styles')
    <style>
        :root {
            --primary-color: var(--color-blue);
            --primary-hover: var(--color-blue);
            --secondary-color: #f8f9fa;
            --text-color: #2d3748;
            --light-text: #718096;
            --border-color: #e2e8f0;
            --error-color: #e53e3e;
            --success-color: #38a169;
        }

        body {
            background-color: #f5f7fa;
        }

        #main {
            margin-top: 45px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 10px 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(108, 99, 255, 0.2);
        }


        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }

        .required__field {
            color: var(--error-color);
            font-size: 0.9em;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            margin-bottom: 20px;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.05);
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Image input styling */
        .image-input-container {
            text-align: center;
            max-width: 100%;
            margin: 0 auto;
            position: relative;
        }

        .image-input-container .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            align-items: center;
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 20px;
            min-height: 180px;
            background-color: var(--sidenavbar-body-color);
            margin-top: 15px;
            overflow-y: auto;
            text-align: center;
            position: relative;
            transition: border-color 0.3s ease;
        }

        .image-input-container .image-preview:hover {
            border-color: var(--primary-color);
        }

        .image-preview p {
            flex-basis: 100%;
            text-align: center;
            font-size: 16px;
            color: var(--light-text);
            margin: 0;
        }

        .image-input-container .image-item {
            position: relative;
            width: 140px;
            height: 140px;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .image-input-container .image-item:hover {
            transform: scale(1.03);
        }

        .image-input-container .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview .upload-btn {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background-color: var(--primary-color);
            color: white !important;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .image-preview .upload-btn:hover {
            background-color: var(--primary-hover);
            transform: scale(1.1);
        }

        .image-input-container input[type="file"] {
            display: none;
        }

        .image-input-container .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background-color: rgba(229, 62, 62, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            opacity: 0;
        }

        .image-item:hover .remove-btn {
            opacity: 1;
        }

        .image-input-container .remove-btn:hover {
            background-color: var(--error-color);
            transform: scale(1.1);
        }

        /* Unit details styling */
        #unit-details {
            background-color: var(--body-background-color);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        #unit-details p {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        #unit-details strong {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .avatar {
                width: 100px;
                height: 100px;
            }

            .image-input-container .image-item {
                width: 100px;
                height: 100px;
            }
        }

        /* Animation for form sections */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.4s ease forwards;
        }

        /* Custom scrollbar for image preview */
        .image-preview::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .image-preview::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .image-preview::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .image-preview::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* Status indicators */
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .status-available {
            background-color: var(--success-color);
        }

        .status-unavailable {
            background-color: var(--error-color);
        }

        /* Form labels */
        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 8px;
        }

        /* Unit image styling */
        #unitImage {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        #unitImage:hover {
            transform: scale(1.02);
        }
    </style>
@endpush

@section('content')
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.units.index'), 'label' => 'Units'],
            ['url' => '', 'label' => 'Assign Unit']
        ]"
    />
    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['AssignUnit']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="container mt-2">
                    <form action="{{ route('owner.assignunits.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Left Column - User Details -->
                            <div class="col-md-6">
                                <div class="card shadow p-4 mb-4">
                                    <h5 class="card-title mb-4 text-primary">
                                        <i class='bx bxs-user-detail me-2'></i> User Information
                                    </h5>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label for="user_id" class="form-label">Select User</label>
                                                <select class="form-select" id="user_id" name="userId" required>
                                                    <option value="">Select a user</option>
                                                    @if(!empty($users) && count($users))
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}" data-email="{{ $user->email }}"
                                                                {{ old('userId') == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }} ({{ $user->email }})
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" disabled>No user found</option>
                                                    @endif
                                                </select>
                                                @error('userId')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_name" class="form-label">Name <span class="required__field">*</span></label>
                                                <div class="position-relative">
                                                    <input type="text" name="user_name" id="user_name" class="form-control @error('user_name') is-invalid @enderror"
                                                           value="{{ old('user_name') }}" maxlength="50" placeholder="User Name" required>
                                                    <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_email" class="form-label">Email <span class="required__field">*</span></label>
                                                <div class="position-relative">
                                                    <input type="email" name="user_email" id="user_email" class="form-control @error('user_email') is-invalid @enderror"
                                                           value="{{ old('user_email') }}" placeholder="Email" maxlength="50" required>
                                                    <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_email')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_contact" class="form-label">Phone Number</label>
                                                <div class="position-relative">
                                                    <input type="text" name="user_phone_no" id="user_contact" value="{{ old('user_phone_no') }}"
                                                           class="form-control contact" placeholder="0312-3456789" maxlength="14">
                                                    <i class='bx bxs-mobile input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_phone_no')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_cnic" class="form-label">CNIC</label>
                                                <div class="position-relative">
                                                    <input type="text" name="user_cnic" id="user_cnic" class="form-control @error('user_cnic') is-invalid @enderror"
                                                           value="{{ old('user_cnic') }}" maxlength="15" placeholder="12345-1234567-1">
                                                    <i class='bx bxs-id-card input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_cnic')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_gender" class="form-label">Gender <span class="required__field">*</span></label>
                                                <select name="user_gender" id="user_gender" class="form-select" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male" {{ old('user_gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ old('user_gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                    <option value="Other" {{ old('user_gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('user_gender')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_date_of_birth" class="form-label">Date of Birth</label>
                                                <input type="date" class="form-control" id="user_date_of_birth" name="user_date_of_birth"
                                                       value="{{ old('user_date_of_birth', date('Y-m-d')) }}">
                                                @error('user_date_of_birth')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label for="user_location" class="form-label">Location</label>
                                                <div class="position-relative">
                                                    <input type="text" name="user_location" id="user_location" class="form-control @error('user_location') is-invalid @enderror"
                                                           value="{{ old('user_location') }}" maxlength="100" placeholder="Enter Location">
                                                    <i class='bx bxs-edit-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_location')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="country" class="form-label">Country</label>
                                                <select class="form-select" id="country" name="user_country">
                                                    <option value="" selected>Select Country</option>
                                                </select>
                                                @error('user_country')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="province" class="form-label">Province</label>
                                                <select class="form-select" id="province" name="user_province">
                                                    <option value="" selected>Select Province</option>
                                                </select>
                                                @error('user_province')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <select class="form-select" id="city" name="user_city">
                                                    <option value="" selected>Select City</option>
                                                </select>
                                                @error('user_city')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label for="user_postal_code" class="form-label">Postal Code</label>
                                                <div class="position-relative">
                                                    <input type="text" name="user_postal_code" id="user_postal_code" class="form-control @error('user_postal_code') is-invalid @enderror"
                                                           value="{{ old('user_postal_code') }}" maxlength="100" placeholder="Enter Postal Code">
                                                    <i class='bx bx-current-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_postal_code')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_picture" class="form-label">Profile Picture</label>
                                                <input type="file" name="user_picture" id="user_picture" class="form-control" accept="image/*" onchange="previewImage(event)">
                                                @error('user_picture')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                                            <img id="avatar" class="avatar" src="{{ old('user_picture') ? asset(old('user_picture')) : asset('img/placeholder-profile.png') }}" alt="User Picture">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Unit Details -->
                            <div class="col-md-6">
                                <div class="card shadow p-4 mb-4">
                                    <h5 class="card-title mb-4 text-primary">
                                        <i class='bx bxs-building-house me-2'></i> Unit Information
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="building_id" class="form-label">Building</label>
                                                <select name="buildingId" id="building_id" class="form-select" required>
                                                    <option value="" selected>Select Building</option>
                                                    @forelse($buildings as $building)
                                                        <option value="{{ $building->id }}"
                                                            {{ old('buildingId', $selectedBuildingId) == $building->id ? 'selected' : '' }}>
                                                            {{ $building->name }}
                                                        </option>
                                                    @empty
                                                        <option value="">No building found</option>
                                                    @endforelse
                                                </select>
                                                @error('buildingId')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="unit_id" class="form-label">Unit</label>
                                                <input type="hidden" name="unitName" id="unit_name">
                                                <select class="form-select" id="unit_id" name="unitId" required>
                                                    <option value="" selected>Select Unit</option>
                                                </select>
                                                @error('unitId')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 text-center">
                                            <img id="unitImage" src="{{ asset('img/placeholder-unit.png') }}"
                                                 alt="Unit Image" class="img-fluid rounded"
                                                 style="height: 150px; object-fit: cover;">
                                            <div class="mt-2">
                                                <small class="text-muted">Unit Preview</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <h6 class="mb-3 text-primary">
                                            <i class='bx bxs-detail me-2'></i> Unit Details
                                        </h6>
                                        <div class="row" id="unit-details">
                                            <div class="col-md-4">
                                                <p><strong>Unit Name:</strong> <span id="unitName">-</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Unit Type:</strong> <span id="unitType">-</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Price:</strong> <span id="unitPrice">-</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Sale / Rent:</strong> <span id="unitSaleRent">-</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Status:</strong> <span id="unitStatus">-</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Area:</strong> <span id="unitArea">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow p-4 mb-4">
                                    <h5 class="card-title mb-4 text-primary">
                                        <i class='bx bxs-file-contract me-2'></i> Contract Details
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="contract_type" class="form-label">Contract Type <span class="required__field">*</span></label>
                                                <select name="type" id="contract_type" class="form-select @error('type') is-invalid @enderror" required>
                                                    <option value="">Select Contract Type</option>
                                                    <option value="Rented" {{ old('type') == 'Rented' ? 'selected' : '' }}>Rent</option>
                                                    <option value="Sold" {{ old('type') == 'Sold' ? 'selected' : '' }}>Sale</option>
                                                </select>
                                                @error('type')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="price" class="form-label">Contract Price <span class="required__field">*</span></label>
                                                <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror"
                                                       value="{{ old('price') }}" placeholder="Enter unit price" required>
                                                @error('price')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12 rent-fields d-none" id="duration_field_wrapper">
                                            <div class="form-group mb-3">
                                                <label for="no_of_months" class="form-label">Number of <span id="duration_label">Months</span> <span class="required__field indicator d-none">*</span></label>
                                                <input type="number" class="form-control @error('no_of_months') is-invalid @enderror"   id="no_of_months" name="no_of_months" placeholder="Number of Months" value="{{ old('no_of_months', 1) }}">
                                                @error('no_of_months')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Contract Documents (Max 4 images)</label>
                                                <div class="image-input-container">
                                                    <input type="file" id="image-input" name="pictures[]" accept="image/png, image/jpeg, image/jpg, image/gif" multiple hidden>
                                                    @error('pictures')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                    <div class="image-preview" id="image-preview">
                                                        <p id="image-message">No images selected</p>
                                                        <label for="image-input" class="upload-btn">
                                                            <i class='bx bx-upload'></i>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6 mt-2">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="window.history.back()">
                                    <i class='bx bx-arrow-back me-1'></i> Cancel
                                </button>
                            </div>
                            <div class="col-md-6 mt-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class='bx bx-save me-1'></i> Save Contract
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- All your existing scripts remain unchanged -->
    <!-- Image avatar script -->
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('avatar');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

    <!-- CNIC script -->
    <script>
        document.getElementById('cnic').addEventListener('input', function (e) {
            const x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>

    <!-- Image script -->
    <script>
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        const imageMessage = document.getElementById('image-message');

        // Ensure upload button remains in preview
        const uploadButton = document.createElement('label');
        uploadButton.classList.add('upload-btn');
        uploadButton.setAttribute('for', 'image-input');
        uploadButton.innerHTML = "<i class='bx bx-upload'></i>";
        imagePreview.appendChild(uploadButton);

        imageInput.addEventListener('change', () => {
            const files = imageInput.files;

            if (files.length > 4) {
                imageMessage.textContent = 'You can only select up to 4 images.';
                imageMessage.style.color = 'red';
                imageInput.value = '';
                return;
            }

            // Clear previous preview but keep the upload button
            imagePreview.innerHTML = '';
            imagePreview.appendChild(uploadButton);

            if (files.length > 0) {
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        const imageItem = document.createElement('div');
                        imageItem.classList.add('image-item');
                        imageItem.setAttribute('data-index', index);

                        const img = document.createElement('img');
                        img.src = e.target.result;

                        const removeBtn = document.createElement('button');
                        removeBtn.classList.add('remove-btn');
                        removeBtn.innerHTML = '×';

                        // Remove image on click
                        removeBtn.addEventListener('click', () => {
                            imageItem.remove();
                            if (imagePreview.children.length === 1) { // Only upload button remains
                                imagePreview.innerHTML = '<p>No images selected</p>';
                                imagePreview.appendChild(uploadButton);
                            }
                        });

                        imageItem.appendChild(img);
                        imageItem.appendChild(removeBtn);
                        imagePreview.appendChild(imageItem);
                    };

                    reader.readAsDataURL(file);
                });
            } else {
                imagePreview.innerHTML = '<p>No images selected</p>';
                imagePreview.appendChild(uploadButton);
            }
        });
    </script>

    <!-- Units by Building ID -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const unitSelect = document.getElementById("unit_id");
            const buildingSelect = document.getElementById("building_id");

            // Function to fetch units based on selected building
            function fetchUnits(buildingId, selectedUnit = null) {
                if (!buildingId) return;

                fetch(`{{ route('owner.buildings.units.available', ':id') }}`.replace(':id', buildingId), {
                    method: "GET",
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        unitSelect.innerHTML = `<option value="" selected>Select Unit</option>`;

                        if (data.units.length > 0) {
                            data.units.forEach(unit => {
                                const option = document.createElement("option");
                                option.value = unit.id;
                                option.textContent = unit.unit_name;
                                unitSelect.appendChild(option);
                            });
                        }

                        // Preselect the unit if available and fetch its details
                        if (selectedUnit) {
                            unitSelect.value = selectedUnit;
                            fetchUnitDetails(selectedUnit);
                        }
                    })
                    .catch(error => console.error("Error fetching units:", error));
            }

            // Function to fetch unit details
            function fetchUnitDetails(unitId) {
                if (!unitId) return;

                fetch(`{{ route('owner.units.details', ':id') }}`.replace(':id', unitId), {
                    method: "GET",
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        let unit = data.Unit;
                        document.getElementById('unitImage').src = unit.pictures.length > 0 ? '/' + unit.pictures[0].file_path : 'default-image.jpg';
                        document.getElementById('unitName').textContent = unit.unit_name;
                        document.getElementById('unitType').textContent = unit.unit_type;
                        document.getElementById('unitPrice').textContent = unit.price;
                        document.getElementById('unitSaleRent').textContent = unit.sale_or_rent;
                        document.getElementById('unitStatus').textContent = unit.status;
                        document.getElementById('unitArea').textContent = unit.area;
                        document.getElementById('unit_name').value = unit.unit_name;
                    })
                    .catch(error => console.error("Error fetching unit details:", error));
            }

            // Get initial values from PHP variables
            const selectedBuilding = `{{ old('buildingId', $selectedBuildingId) }}`;
            const selectedUnit = `{{ old('unitId', $selectedUnitId) }}`;

            // Fetch units and details on page load if selectedBuilding and selectedUnit exist
            if (selectedBuilding) {
                fetchUnits(selectedBuilding, selectedUnit);
            }

            // Event listener for building change
            buildingSelect.addEventListener("change", function () {
                fetchUnits(this.value);
            });

            // Event listener for unit change
            unitSelect.addEventListener("change", function () {
                fetchUnitDetails(this.value);
            });
        });
    </script>

    <!--   -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let userSelect = document.getElementById("user_id");
            let countrySelect = document.getElementById('country');
            let provinceSelect = document.getElementById('province');
            let citySelect = document.getElementById('city');

            const dropdownData = @json($dropdownData);

            function populateCountries() {
                countrySelect.innerHTML = '<option value="" selected>Select Country</option>';

                dropdownData.forEach(country => {
                    let option = document.createElement('option');
                    option.value = country.values[0]?.value_name || 'Unnamed Country';
                    option.dataset.id = country.id;
                    option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                    countrySelect.appendChild(option);
                });
            }

            function populateProvinces() {
                provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                let selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id;
                let selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    selectedCountry.values.forEach(province => {
                        province.childs.forEach(childProvince => {
                            let option = document.createElement('option');
                            option.value = childProvince.value_name;
                            option.dataset.id = childProvince.id;
                            option.textContent = childProvince.value_name;
                            provinceSelect.appendChild(option);
                        });
                    });
                }
            }

            function populateCities() {
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                let selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id;
                let selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    let selectedProvinceId = provinceSelect.options[provinceSelect.selectedIndex]?.dataset.id;
                    let selectedProvince = selectedCountry.values
                        .flatMap(province => province.childs)
                        .find(p => p.id == selectedProvinceId);

                    if (selectedProvince) {
                        selectedProvince.childs.forEach(city => {
                            let option = document.createElement('option');
                            option.value = city.value_name;
                            option.dataset.id = city.id;
                            option.textContent = city.value_name;
                            citySelect.appendChild(option);
                        });
                    }
                }
            }

            function fetchUserDetails(userId) {
                if (!userId) return;

                fetch(`{{ route('users.show', ':id') }}`.replace(':id', userId), {
                    method: "GET",
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        let user = data.user;

                        let placeholderImage = "{{ asset('assets/placeholder-profile.png') }}";
                        let baseAssetPath = "{{ asset('/') }}";

                        document.getElementById("avatar").src = user.picture ? baseAssetPath + user.picture : placeholderImage;
                        document.getElementById("user_name").value = user.name;
                        document.getElementById("user_email").value = user.email;
                        document.getElementById("user_contact").value = user.phone_no;
                        document.getElementById("user_cnic").value = user.cnic;
                        document.getElementById("user_gender").value = user.gender;
                        document.getElementById("user_date_of_birth").value = user.date_of_birth;
                        document.getElementById("user_location").value = user.address.location;
                        document.getElementById("user_postal_code").value = user.address.postal_code;

                        console.log("User's Country:", user.address.country);
                        console.log("User's Province:", user.address.province);
                        console.log("User's City:", user.address.city);

                        countrySelect.value = user.address.country;
                        populateProvinces();

                        setTimeout(() => {
                            provinceSelect.value = user.address.province;
                            populateCities();

                            setTimeout(() => {
                                citySelect.value = user.address.city;
                            }, 500);
                        }, 500);
                    })
                    .catch(error => console.error("Error fetching user details:", error));
            }

            userSelect.addEventListener("change", function () {
                fetchUserDetails(this.value);
            });

            populateCountries();

            if (userSelect.value) {
                fetchUserDetails(userSelect.value);
            }

            countrySelect.addEventListener('change', populateProvinces);
            provinceSelect.addEventListener('change', populateCities);
        });
    </script>

    <!-- User detail edit enable disable-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let userDropdown = document.getElementById("user_id"); // Change this ID to match your user dropdown
            let userPictureInput = document.getElementById("user_picture");
            let avatar = document.getElementById("avatar");

            // List of form fields by ID
            let fields = [
                "user_name",
                "user_email",
                "user_contact",
                "user_cnic",
                "user_gender",
                "user_date_of_birth",
                "user_location",
                "country",
                "province",
                "city",
                "user_postal_code",
                "user_picture",
            ];

            function toggleInputs(isDisabled) {
                fields.forEach(id => {
                    let field = document.getElementById(id);
                    if (field) {
                        field.readOnly = isDisabled;
                        field.disabled = isDisabled;
                        if (!isDisabled) {
                            field.value = ""; // Clear text inputs
                            if (field.tagName === "SELECT") {
                                field.selectedIndex = 0; // Reset dropdowns
                            }
                            if (field.type === "file") {
                                field.value = null; // Clear file inputs
                            }
                        }
                    }
                });

                // Reset image preview
                if (!isDisabled) {
                    avatar.src = "{{ asset('img/placeholder-profile.png') }}"; // Set default image
                }
            }

            userDropdown.addEventListener("change", function () {
                let hasSelection = this.value !== "";
                toggleInputs(hasSelection);
            });

            // Image preview function
            window.previewImage = function (event) {
                let reader = new FileReader();
                reader.onload = function () {
                    avatar.src = reader.result;
                };
                if (event.target.files.length) {
                    reader.readAsDataURL(event.target.files[0]);
                } else {
                    avatar.src = "{{ asset('img/placeholder-profile.png') }}"; // Reset image if no file is selected
                }
            };

            // Initial check on page load
            toggleInputs(userDropdown.value !== "");
        });
    </script>

    <!-- Sold/ Rented Contract type -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contractType = document.getElementById('contract_type');
            const rentFields = document.querySelectorAll('.rent-fields');
            const purchaseField = document.querySelector('.purchase-field');
            const indicators = document.querySelectorAll('.required__field.indicator');
            const noOfMonthsField = document.getElementById('no_of_months');

            // Store the original value when changing away from Rent
            let storedMonthsValue = noOfMonthsField.value || 1;

            function toggleFields() {
                const value = contractType.value;
                if (value === 'Rented') {
                    // Show rent fields
                    rentFields.forEach(field => field.classList.remove('d-none'));
                    if (purchaseField) purchaseField.classList.add('d-none');
                    indicators.forEach(el => el.classList.remove('d-none'));

                    // Enable and restore field attributes
                    noOfMonthsField.disabled = false;
                    noOfMonthsField.setAttribute('name', 'no_of_months');

                    // Restore the stored value if we have one
                    if (storedMonthsValue) {
                        noOfMonthsField.value = storedMonthsValue;
                    }
                } else if (value === 'Sold') {
                    // Show purchase fields
                    if (purchaseField) purchaseField.classList.remove('d-none');
                    rentFields.forEach(field => field.classList.add('d-none'));
                    indicators.forEach(el => el.classList.remove('d-none'));

                    // Store the current value before disabling
                    storedMonthsValue = noOfMonthsField.value;

                    // Disable and prevent submission
                    noOfMonthsField.disabled = true;
                    noOfMonthsField.removeAttribute('name');
                } else {
                    // Default case (no selection)
                    if (purchaseField) purchaseField.classList.add('d-none');
                    rentFields.forEach(field => field.classList.add('d-none'));
                    indicators.forEach(el => el.classList.add('d-none'));

                    // Disable and prevent submission
                    noOfMonthsField.disabled = true;
                    noOfMonthsField.removeAttribute('name');
                }
            }

            // Initialize based on the old value (for validation errors)
            function initializeFields() {
                const oldContractType = "{{ old('contract_type') }}";
                if (oldContractType === 'Rented') {
                    contractType.value = 'Rented';
                    // Ensure the field is visible and enabled
                    rentFields.forEach(field => field.classList.remove('d-none'));
                    noOfMonthsField.disabled = false;
                    noOfMonthsField.setAttribute('name', 'no_of_months');
                } else if (oldContractType === 'Sold') {
                    contractType.value = 'Sold';
                    // Ensure the field is hidden and disabled
                    rentFields.forEach(field => field.classList.add('d-none'));
                    noOfMonthsField.disabled = true;
                    noOfMonthsField.removeAttribute('name');
                }
            }

            contractType.addEventListener('change', toggleFields);

            // Initialize on page load
            initializeFields();
            toggleFields();
        });
    </script>
@endpush
