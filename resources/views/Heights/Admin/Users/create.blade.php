@extends('layouts.app')

@section('title', 'Add User')

@push('styles')
    <style>
        :root {
            --primary-color: #6c63ff;
            --primary-hover: #5752d3;
            --secondary-color: #f8f9fa;
            --text-color: #2d3748;
            --light-text: #718096;
            --border-color: #e2e8f0;
            --error-color: #e53e3e;
            --success-color: #38a169;
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
            padding: 5px 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(108, 99, 255, 0.2);
        }

        .input-icon {
            color: var(--sidenavbar-text-color);
            transition: color 0.3s ease;
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

        /*.btn-primary {*/
        /*    background-color: var(--primary-color);*/
        /*    border-color: var(--primary-color);*/
        /*}*/

        /*.btn-primary:hover {*/
        /*    background-color: var(--primary-hover);*/
        /*    border-color: var(--primary-hover);*/
        /*    transform: translateY(-2px);*/
        /*}*/

        .btn-secondary {
            background-color: white;
            border-color: var(--border-color);
            color: var(--text-color);
        }

        .btn-secondary:hover {
            background-color: #252525;
            color: #ffff;
            border-color: var(--border-color);
        }

        /* Form labels */
        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 8px;
            display: block;
        }

        /* Section headers */
        .section-header {
            color: var(--main-text-color);
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-header i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        /* Error messages */
        .invalid-feedback {
            font-size: 0.85em;
            margin-top: 5px;
        }

        /* Animation for form sections */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-section {
            animation: fadeIn 0.4s ease forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .avatar {
                width: 100px;
                height: 100px;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('users.index'), 'label' => 'Users'],
            ['url' => '', 'label' => 'Create user']
        ]"
    />
    <x-Admin.side-navbar :openSections="['AdminControl', 'UserManagement']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0 text-primary">
                                        <i class='bx bxs-user-plus me-2'></i>Create New User
                                    </h4>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        <i class='bx bx-arrow-back me-1'></i> Back to Users
                                    </a>
                                </div>

                                <div class="card shadow py-2 px-4 mb-5 bg-body rounded">
                                    <div class="card-body">
                                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                                            @csrf

                                            <div class="form-section">
                                                <h5 class="section-header">
                                                    <i class='bx bxs-user-detail'></i> Basic Information
                                                </h5>

                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="name" class="form-label">Name <span class="required__field">*</span></label>
                                                            <div class="position-relative">
                                                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                                                       value="{{ old('name') }}" maxlength="50" placeholder="User Name" required>
                                                                <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="email" class="form-label">Email <span class="required__field">*</span></label>
                                                            <div class="position-relative">
                                                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                                                       value="{{ old('email') }}" placeholder="Email" maxlength="50" required>
                                                                <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="gender" class="form-label">Gender <span class="required__field">*</span></label>
                                                            <select name="gender" id="gender" class="form-select" value="{{ old('gender') }}" required>
                                                                <option value="">Select Gender</option>
                                                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                            @error('gender')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="contact" class="form-label">Phone Number</label>
                                                            <div class="position-relative">
                                                                <input type="text" name="phone_no" id="contact" value="{{ old('phone_no') }}"
                                                                       class="form-control contact" placeholder="0312-3456789" maxlength="14">
                                                                <i class='bx bxs-mobile input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('phone_no')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="cnic" class="form-label">CNIC <span class="required__field">*</span></label>
                                                            <div class="position-relative">
                                                                <input type="text" name="cnic" id="cnic" class="form-control @error('cnic') is-invalid @enderror"
                                                                       value="{{ old('cnic') }}" maxlength="15" placeholder="12345-1234567-1" required>
                                                                <i class='bx bxs-id-card input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('cnic')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                                                   value="{{ old('date_of_birth', date('Y-m-d')) }}">
                                                            @error('date_of_birth')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="role_id" class="form-label">Role <span class="required__field">*</span></label>
                                                            <select class="form-select" id="role_id" name="role_id" value="{{ old('role_id') }}" required>
                                                                <option value="" disabled {{ old('role_id') === null ? 'selected' : '' }}>Select Role</option>
                                                                @foreach($roles as $id => $role)
                                                                    <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>
                                                                        {{ $role }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('role_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-section mt-4">
                                                <h5 class="section-header">
                                                    <i class='bx bxs-map'></i> Address Information
                                                </h5>

                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <label for="country" class="form-label">Country</label>
                                                            <select class="form-select" id="country" name="country">
                                                                <option value="" selected>Select Country</option>
                                                            </select>
                                                            @error('country')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <label for="province" class="form-label">Province</label>
                                                            <select class="form-select" id="province" name="province">
                                                                <option value="" selected>Select Province</option>
                                                            </select>
                                                            @error('province')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <label for="city" class="form-label">City</label>
                                                            <select class="form-select" id="city" name="city">
                                                                <option value="" selected>Select City</option>
                                                            </select>
                                                            @error('city')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                                        <div class="form-group mb-3">
                                                            <label for="location" class="form-label">Location</label>
                                                            <div class="position-relative">
                                                                <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror"
                                                                       value="{{ old('location') }}" maxlength="100" placeholder="Enter Location">
                                                                <i class='bx bxs-edit-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('location')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                                        <div class="form-group mb-3">
                                                            <label for="postal_code" class="form-label">Postal Code</label>
                                                            <div class="position-relative">
                                                                <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                                                                       value="{{ old('postal_code') }}" maxlength="100" placeholder="Enter Postal Code">
                                                                <i class='bx bx-current-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('postal_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-section mt-4">
                                                <h5 class="section-header">
                                                    <i class='bx bxs-image'></i> Profile Picture
                                                </h5>

                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="picture" class="form-label">Upload Picture</label>
                                                            <input type="file" name="picture" id="picture" class="form-control"
                                                                   accept="image/*" onchange="previewImage(event)">
                                                            @error('picture')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-6 col-lg-4 d-flex flex-column align-items-center">
                                                        <img id="avatar" class="avatar"
                                                             src="{{ old('picture') ? asset(old('picture')) : asset('img/placeholder-profile.png') }}"
                                                             alt="User Picture Preview">
                                                        <small class="text-muted mt-2">Image Preview</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="submit" class="btn btn-primary px-4">
                                                    <i class='bx bx-save me-1'></i> Create User
                                                </button>
                                            </div>
                                        </form>
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

    <!-- Cnic script -->
    <script>
        document.getElementById('cnic').addEventListener('input', function (e) {
            const x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>

    <!-- Country/Province/City dropdowns  -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countrySelect = document.getElementById('country');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            // Dropdown data passed from the controller
            const dropdownData = @json($dropdownData);

            // Populate Country Dropdown
            dropdownData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.values[0]?.value_name || 'Unnamed Country';
                option.dataset.id = country.id; // Store ID in a data attribute
                option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                countrySelect.appendChild(option);
            });

            // Handle Country Change
            countrySelect.addEventListener('change', function () {
                provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = this.options[this.selectedIndex]?.dataset.id; // Retrieve ID from data attribute
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    selectedCountry.values.forEach(province => {
                        province.childs.forEach(childProvince => {
                            const option = document.createElement('option');
                            option.value = childProvince.value_name; // Use value_name for option value
                            option.dataset.id = childProvince.id; // Store ID in a data attribute
                            option.textContent = childProvince.value_name;
                            provinceSelect.appendChild(option);
                        });
                    });
                }
            });

            // Handle Province Change
            provinceSelect.addEventListener('change', function () {
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id; // Retrieve ID from data attribute
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    const selectedProvinceId = this.options[this.selectedIndex]?.dataset.id; // Retrieve ID from data attribute
                    const selectedProvince = selectedCountry.values
                        .flatMap(province => province.childs)
                        .find(p => p.id == selectedProvinceId);

                    if (selectedProvince) {
                        selectedProvince.childs.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.value_name; // Use value_name for option value
                            option.dataset.id = city.id; // Store ID in a data attribute
                            option.textContent = city.value_name;
                            citySelect.appendChild(option);
                        });
                    }
                }
            });
        });
    </script>
@endpush
