@extends('layouts.app')

@section('title', 'Add User')

@push('styles')
    <style>
        :root {
            --primary-color: var(--color-blue);
            --primary-hover: var(--color-blue);
            --secondary-color: #f8f9fa;
            --text-color: var(--sidenavbar-text-color);
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

        /* Form labels */
        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 8px;
            display: block;
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
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('users.index'), 'label' => 'Users'],
            ['url' => '', 'label' => 'Create user']
        ]"
    />
    <x-Admin.side-navbar :openSections="['AdminControl', 'UserManagement']" />
    <x-error-success-model />

    @php
        $maxDate = \Carbon\Carbon::now()->subYears(10)->format('Y-m-d');
    @endphp

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">
                                Create New User
                            </h4>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Go Back</a>
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
                                                    <label for="contact" class="form-label">Phone Number <span class="required__field">*</span></label>
                                                    <div class="position-relative">
                                                        <input type="text" name="phone_no" id="contact" value="{{ old('phone_no') }}"
                                                               class="form-control contact" placeholder="0300-0000000" maxlength="12" required>
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
                                                    <label for="date_of_birth" class="form-label">
                                                        Date of Birth <span class="required__field">*</span>
                                                    </label>
                                                    <input type="date"
                                                           class="form-control @error('date_of_birth') is-invalid @enderror"
                                                           id="date_of_birth"
                                                           name="date_of_birth"
                                                           max="{{ $maxDate }}"
                                                           value="{{ old('date_of_birth', date('Y-m-d', strtotime($maxDate))) }}"
                                                           required>
                                                    <div id="dob_error" class="invalid-feedback d-none">
                                                        User must be at least 10 years old.
                                                    </div>
                                                    @error('date_of_birth')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-6 col-lg-4">
                                                <div class="form-group mb-3">
                                                    <label for="role_id" class="form-label">Role <span class="required__field">*</span></label>
                                                    <select class="form-select" id="role_id" name="role_id" required>
                                                        <option value="" disabled {{ old('role_id') === null ? 'selected' : '' }}>Select Role</option>
                                                        @forelse($roles ?? [] as $role)
                                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                                {{ $role->name }}
                                                            </option>
                                                        @empty
                                                        @endforelse
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
                                                    <label for="country" class="form-label">Country <span class="required__field">*</span></label>
                                                    <select class="form-select" id="country" name="country" required>
                                                        <option value="" selected>Select Country</option>
                                                    </select>
                                                    @error('country')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label for="province" class="form-label">Province <span class="required__field">*</span></label>
                                                    <select class="form-select" id="province" name="province" required>
                                                        <option value="" selected>Select Province</option>
                                                    </select>
                                                    @error('province')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label for="city" class="form-label">City <span class="required__field">*</span></label>
                                                    <select class="form-select" id="city" name="city" required>
                                                        <option value="" selected>Select City</option>
                                                    </select>
                                                    @error('city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-6 col-lg-8">
                                                <div class="form-group mb-3">
                                                    <label for="location" class="form-label">Location <span class="required__field">*</span></label>
                                                    <div class="position-relative">
                                                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror"
                                                               value="{{ old('location') }}" maxlength="100" placeholder="Enter Location" required>
                                                        <i class='bx bxs-edit-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                    </div>
                                                    @error('location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-6 col-lg-4">
                                                <div class="form-group mb-3">
                                                    <label for="postal_code" class="form-label">Postal Code <span class="required__field">*</span></label>
                                                    <div class="position-relative">
                                                        <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                                                               value="{{ old('postal_code') }}" maxlength="100" placeholder="Enter Postal Code" required>
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

    <!-- Country/Province/City dropdowns  -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countrySelect = document.getElementById('country');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            const dropdownData = @json($dropdownData);

            // Populate Country Dropdown
            dropdownData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.values[0]?.value_name || 'Unnamed Country';
                option.dataset.id = country.id;
                option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                countrySelect.appendChild(option);
            });

            // Change Listeners (as you already have)
            countrySelect.addEventListener('change', function () {
                provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = this.options[this.selectedIndex]?.dataset.id;
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    selectedCountry.values.forEach(province => {
                        province.childs.forEach(childProvince => {
                            const option = document.createElement('option');
                            option.value = childProvince.value_name;
                            option.dataset.id = childProvince.id;
                            option.textContent = childProvince.value_name;
                            provinceSelect.appendChild(option);
                        });
                    });
                }
            });

            provinceSelect.addEventListener('change', function () {
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id;
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    const selectedProvinceId = this.options[this.selectedIndex]?.dataset.id;
                    const selectedProvince = selectedCountry.values
                        .flatMap(province => province.childs)
                        .find(p => p.id == selectedProvinceId);

                    if (selectedProvince) {
                        selectedProvince.childs.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.value_name;
                            option.dataset.id = city.id;
                            option.textContent = city.value_name;
                            citySelect.appendChild(option);
                        });
                    }
                }
            });

            // Repopulate Old Values Only if Error Occurred
            const oldCountry = @json(old('country'));
            const oldProvince = @json(old('province'));
            const oldCity = @json(old('city'));

            if (oldCountry || oldProvince || oldCity) {
                function populateOldSelections() {
                    const countryOption = [...countrySelect.options].find(opt => opt.value === oldCountry);
                    if (countryOption) {
                        countryOption.selected = true;
                        countrySelect.dispatchEvent(new Event('change'));

                        setTimeout(() => {
                            const provinceOption = [...provinceSelect.options].find(opt => opt.value === oldProvince);
                            if (provinceOption) {
                                provinceOption.selected = true;
                                provinceSelect.dispatchEvent(new Event('change'));

                                setTimeout(() => {
                                    const cityOption = [...citySelect.options].find(opt => opt.value === oldCity);
                                    if (cityOption) {
                                        cityOption.selected = true;
                                    }
                                }, 200);
                            }
                        }, 200);
                    }
                }

                populateOldSelections();
            }
        });
    </script>
@endpush
