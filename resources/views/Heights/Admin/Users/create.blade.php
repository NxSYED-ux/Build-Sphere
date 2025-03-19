@extends('layouts.app')

@section('title', 'Add User')

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
            margin-bottom: 20px;
            object-fit: cover;
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
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">Create New User</h4>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Go Back</a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " >

                                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="name">Name</label>
                                                        <span class="required__field">*</span><br>
                                                        <div class="position-relative">
                                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" maxlength="50" placeholder="User Name" required>
                                                            <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="email">Email</label>
                                                        <span class="required__field">*</span><br>
                                                        <div class="position-relative">
                                                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Email"  maxlength="50" required>
                                                            <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="gender">Gender</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="gender" id="gender" class="form-select" value="{{ old('gender') }}" required>
                                                            <option value="">Select Gender</option>
                                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                        </select>
                                                        @error('gender')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="contact">Phone no:</label><br>
                                                        <div class="position-relative">
                                                            <input type="text" name="phone_no" id="contact" value="{{ old('phone_no') }}"
                                                                class="form-control contact" placeholder="0312-3456789" maxlength="14">
                                                            <i class='bx bxs-mobile input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                        </div>
                                                        @error('phone_no')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="cnic">CNIC</label>
                                                        <span id="cnic_status"></span><br>
                                                        <div class="position-relative">
                                                            <input type="text" name="cnic" id="cnic" class="form-control @error('cnic') is-invalid @enderror"
                                                                value="{{ old('cnic') }}" maxlength="15" placeholder="12345-1234567-1">
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
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="date_of_birth">Date of Birth</label>
                                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', date('Y-m-d')) }}">
                                                        @error('date_of_birth')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="role_id">Role</label>
                                                        <span class="required__field">*</span><br>
                                                        <select class="form-select" id="role_id" name="role_id" value="{{ old('role_id') }}" required>
                                                            <option value="" disabled {{ old('role_id') === null ? 'selected' : '' }}>Select Role</option>
                                                            @foreach($roles as $id => $role)
                                                                <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>
                                                                    {{ $role }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('role_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!--  -->
                                            <div class="row">
                                                <!--  -->
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="country" class="form-label">Country</label>
                                                        <select class="form-select" id="country" name="country">
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
                                                        <select class="form-select" id="province" name="province">
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
                                                        <select class="form-select" id="city" name="city">
                                                            <option value="" selected>Select Province</option>
                                                        </select>
                                                        @error('city')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="location">Location</label>
                                                        <div class="position-relative">
                                                            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" maxlength="100" placeholder="Enter Location">
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
                                                        <label for="postal_code">Postal Code</label>
                                                        <div class="position-relative">
                                                            <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}" maxlength="100" placeholder="Enter Postal Code">
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

                                            <!--  -->
                                            <div class="row">
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-2">
                                                        <label for="picture">Picture</label>
                                                        <input type="file" name="picture" id="picture" class="form-control" accept="image/*" onchange="previewImage(event)">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-6 col-lg-4 d-flex align-items-center">
                                                    <img id="avatar" class="avatar" src="{{ old('picture') ? asset(old('picture')) : asset('img/placeholder-profile.png') }}" alt="User Picture">
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Create User</button>
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
