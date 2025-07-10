@extends('layouts.app')

@section('title', 'Edit User')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
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

        .required__field {
            color: var(--error-color);
            font-size: 0.9em;
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        /* Section headers */
        .section-header {
            color: var(--sidenavbar-text-color);
            font-weight: 500;
            margin-bottom: 15px;
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

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('users.index'), 'label' => 'Users'],
            ['url' => '', 'label' => 'Edit User']
        ]"
    />
    <!--  -->
    <x-Admin.side-navbar :openSections="['AdminControl', 'UserManagement']" />
    <x-error-success-model />

    @php
        $maxDate = \Carbon\Carbon::now()->subYears(10)->format('Y-m-d');
    @endphp

    <div id="main">
        @php
        $errorsFromQuery = request()->get('errors', []);
        @endphp

        <section class="content my-3 mx-2">
            <div class="container-fluid ">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Edit User</h4>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Go Back</a>
                        </div>
                        <div class="card shadow p-2 px-4 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body">
                                        <form action="{{ route('users.update')}}" method="POST" enctype="multipart/form-data">
                                            @method('PUT')
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <input type="hidden" name="updated_at" value="{{ $user->updated_at }}">

                                            <div class="form-section">
                                                <h5 class="section-header">
                                                    <i class='bx bxs-user-detail'></i> Basic Information
                                                </h5>
                                                <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="name" class="form-label">Name</label>
                                                        <span class="required__field">*</span>
                                                        <div class="position-relative">
                                                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" maxlength="50" placeholder="User Name" required>
                                                            <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('name')
                                                            <span class="invalid-feedback text-danger" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="email" class="form-label">Email</label>
                                                        <span class="required__field">*</span><br>
                                                        <div class="position-relative">
                                                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" maxlength="50" placeholder="Email" required>
                                                            <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('email')
                                                            <span class="invalid-feedback text-danger" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="gender" class="form-label">Gender</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="gender" id="gender" class="form-select" required>
                                                            <option value="">Select Gender</option>
                                                            <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                                            <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                                            <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                                        </select>
                                                        @error('gender')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="contact" class="form-label">Phone no:</label>
                                                        <span class="required__field">*</span>
                                                        <div class="position-relative">
                                                            <input type="text" name="phone_no" id="contact" value="{{ old('phone_no', $user->phone_no) }}" class="form-control contact" placeholder="0300-0000000" maxlength="12" required>
                                                            <i class='bx bxs-mobile input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('phone_no')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="cnic" class="form-label">CNIC</label>
                                                        <span class="required__field">*</span>
                                                        <div class="position-relative">
                                                            <input type="text" name="cnic" id="cnic" class="form-control @error('cnic') is-invalid @enderror" value="{{ old('cnic', $user->cnic) }}" maxlength="18" placeholder="123-4567-1234567-1" required>
                                                            <i class='bx bxs-id-card input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('cnic')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                                        <span class="required__field">*</span>
                                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" max="{{ $maxDate }}" value="{{ old('date_of_birth', isset($user->date_of_birth) ? \Carbon\Carbon::parse($user->date_of_birth )->format('Y-m-d') : '') }}" required>
                                                        <div id="dob_error" class="invalid-feedback d-none">
                                                            User must be at least 10 years old.
                                                        </div>
                                                        @error('date_of_birth')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="status" class="form-label">Status</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="status" id="status" class="form-select" required>
                                                            <option value="1" {{ old('status', $user->status) == '1' ? 'selected' : '' }}>Active</option>
                                                            <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                        @error('status')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="role_id" class="form-label">Role</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="role_id" id="role_id" class="form-select" required>
                                                            @forelse($roles ?? [] as $role)
                                                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                                    {{ $role->name }}
                                                                </option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                        @error('role_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            </div>

                                            <!--  -->
                                            <div class="form-section mt-4">
                                                <h5 class="section-header">
                                                    <i class='bx bxs-map'></i> Address Information
                                                </h5>
                                                <div class="row">

                                                <!--  -->
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="country" class="form-label">Country</label>
                                                        <span class="required__field">*</span>
                                                        <select class="form-select" id="country" name="country" required>
                                                            <option value="" selected>Select Country</option>
                                                        </select>
                                                        @error('country')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="province" class="form-label">Province</label>
                                                        <span class="required__field">*</span>
                                                        <select class="form-select" id="province" name="province" required>
                                                            <option value="" selected>Select Province</option>
                                                        </select>
                                                        @error('province')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="city" class="form-label">City</label>
                                                        <span class="required__field">*</span>
                                                        <select class="form-select" id="city" name="city" required>
                                                            <option value="" selected>Select Province</option>
                                                        </select>
                                                        @error('customer_city')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-8">
                                                    <div class="form-group mb-3">
                                                        <label for="location" class="form-label">Location</label>
                                                        <span class="required__field">*</span>
                                                        <div class="position-relative">
                                                            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $user->address->location ) }}" maxlength="100" placeholder="Enter Location" required>
                                                            <i class='bx bxs-edit-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('location')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="postal_code" class="form-label">Postal Code</label>
                                                        <span class="required__field">*</span>
                                                        <div class="position-relative">
                                                            <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $user->address->postal_code ) }}" maxlength="100" placeholder="Enter Postal Code" required>
                                                            <i class='bx bx-current-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('postal_code')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            </div>

                                            <div class="form-section mt-4">
                                                <h5 class="section-header">
                                                    <i class='bx bxs-image'></i> Profile Picture
                                                </h5>
                                                <div class="row mb-3">
                                                <div class="col-sm-12 col-md-6 col-lg-4 ">
                                                    <div class="form-group">
                                                        <label for="picture" class="form-label">Picture <span style="font-size: 12px;">(Leave blank to keep current picture)</span></label>
                                                        <input type="file" name="picture" id="picture" class="form-control" onchange="previewImage(event)">
                                                        @error('picture')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-6 col-lg-4 mt-2  d-flex justify-content-center align-items-center">
                                                    <img id="avatar" class="avatar" src="{{ asset($user->picture ??  asset('img/placeholder-profile.png')) }}" alt="User Picture">
                                                </div>
                                            </div>
                                            </div>

                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="submit" class="btn btn-primary px-4">
                                                    <i class='bx bx-save me-1'></i> Update User
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('scripts')

    <!-- Password Script -->
    <script>
        document.getElementById('password').addEventListener('input', function() {
            const passwordField = this;
            const passwordHelp = document.getElementById('passwordHelp');

            if (passwordField.value.length === 0) {
                passwordField.classList.remove('is-invalid');
                passwordHelp.textContent = '';
            } else if (passwordField.value.length < 8) {
                passwordField.classList.add('is-invalid');
                passwordHelp.textContent = 'Password must be at least 8 characters long.';
            } else {
                passwordField.classList.remove('is-invalid');
                passwordHelp.textContent = '';
            }
        });

    </script>

    <script>
        document.getElementById('cnic').addEventListener('input', function (e) {
            const x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
        // Phone number formatting
        document.getElementById('contact').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,7})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
        });

        document.addEventListener('DOMContentLoaded', function () {
            const dobInput = document.getElementById('date_of_birth');
            const errorDiv = document.getElementById('dob_error');

            dobInput.addEventListener('change', function () {
                const inputDate = new Date(this.value);
                const today = new Date();
                const tenYearsAgo = new Date();
                tenYearsAgo.setFullYear(today.getFullYear() - 10);

                if (inputDate > tenYearsAgo) {
                    this.classList.add('is-invalid');
                    errorDiv.classList.remove('d-none');
                } else {
                    this.classList.remove('is-invalid');
                    errorDiv.classList.add('d-none');
                }
            });
        });
    </script>

    <!-- Image avatr script -->
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const avatar = document.getElementById('avatar');
                avatar.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success') || session('error'))
                const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
                messageModal.show();
            @endif
        });
    </script>

    <!-- Country/Province/City dropdowns  -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countrySelect = document.getElementById('country');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            const dropdownData = @json($dropdownData);
            const userCountry = "{{ $user->address->country }}";
            const userProvince = "{{ $user->address->province }}";
            const userCity = "{{ $user->address->city }}";

            // Populate Country Dropdown
            dropdownData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.values[0]?.value_name || 'Unnamed Country'; // Use value_name for the value
                option.dataset.id = country.id; // Store ID in a data attribute
                option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                if (option.value === userCountry) {
                    option.selected = true; // Pre-select user's country
                }
                countrySelect.appendChild(option);
            });

            // Populate Provinces
            function populateProvinces() {
                provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id; // Retrieve ID from data attribute
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    selectedCountry.values.forEach(province => {
                        province.childs.forEach(childProvince => {
                            const option = document.createElement('option');
                            option.value = childProvince.value_name; // Use value_name for the value
                            option.dataset.id = childProvince.id; // Store ID in a data attribute
                            option.textContent = childProvince.value_name;
                            if (option.value === userProvince) {
                                option.selected = true; // Pre-select user's province
                            }
                            provinceSelect.appendChild(option);
                        });
                    });
                }
            }

            // Populate Cities
            function populateCities() {
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id; // Retrieve ID from data attribute
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    const selectedProvinceId = provinceSelect.options[provinceSelect.selectedIndex]?.dataset.id; // Retrieve ID from data attribute
                    const selectedProvince = selectedCountry.values
                        .flatMap(province => province.childs)
                        .find(p => p.id == selectedProvinceId);

                    if (selectedProvince) {
                        selectedProvince.childs.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.value_name; // Use value_name for the value
                            option.dataset.id = city.id; // Store ID in a data attribute
                            option.textContent = city.value_name;
                            if (option.value === userCity) {
                                option.selected = true; // Pre-select user's city
                            }
                            citySelect.appendChild(option);
                        });
                    }
                }
            }

            // Event Listeners
            countrySelect.addEventListener('change', populateProvinces);
            provinceSelect.addEventListener('change', populateCities);

            // Prepopulate Provinces and Cities
            if (userCountry) {
                populateProvinces();
            }
            if (userProvince) {
                populateCities();
            }
        });
    </script>

@endpush
