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

    </style>
@endpush

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('users.index'), 'label' => 'Users'],
            ['url' => '', 'label' => 'Edit User']
        ]"
    />
    <!--  -->
    <x-Admin.side-navbar :openSections="['AdminControl', 'UserManagement']" />
    <x-error-success-model />

    <div id="main">
        @php
        $errorsFromQuery = request()->get('errors', []);
        @endphp

        <section class="content my-3 mx-2">
            <div class="container-fluid ">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">Edit User</h4>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Go Back</a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body">
                                        <form action="{{ route('users.update')}}" method="POST" enctype="multipart/form-data">
                                            @method('PUT')
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <input type="hidden" name="updated_at" value="{{ $user->updated_at }}">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="name">Name</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" maxlength="50" placeholder="User Name" required>
                                                        @error('name')
                                                            <span class="invalid-feedback text-danger" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="email">Email</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" maxlength="50" placeholder="Email" required>
                                                        @error('email')
                                                            <span class="invalid-feedback text-danger" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="gender">Gender</label>
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

                                                <!-- <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="password">Password (Leave blank to keep current password)</label>
                                                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" maxlength="50" placeholder="">
                                                        <small id="passwordHelp" class="form-text text-muted">Password must be at least 8 characters long.</small>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div> -->

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="contact">Phone no:</label><br>
                                                        <input type="text" name="phone_no" id="contact" value="{{ old('phone_no', $user->phone_no) }}" class="form-control contact" placeholder="0312-3456789" maxlength="14">
                                                        @error('phone_no')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="cnic">CNIC</label>
                                                        <input type="text" name="cnic" id="cnic" class="form-control @error('cnic') is-invalid @enderror" value="{{ old('cnic', $user->cnic) }}" maxlength="18" placeholder="123-4567-1234567-1">
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
                                                        <label for="date_of_birth">Date of Birth</label>
                                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', isset($user->date_of_birth) ? \Carbon\Carbon::parse($user->date_of_birth )->format('Y-m-d') : '') }}">
                                                        @error('date_of_birth')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="status">Status</label>
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
                                                        <label for="role_id">Role</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="role_id" id="role_id" class="form-select" required>
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                                    {{ $role->name }} <!-- Adjust according to your role attribute -->
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
                                                        @error('customer_city')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="location">Location</label>
                                                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $user->address->location ) }}" maxlength="100" placeholder="Enter Location">
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
                                                        <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $user->address->postal_code ) }}" maxlength="100" placeholder="Enter Postal Code">
                                                        @error('postal_code')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="picture">Picture (Leave blank to keep current picture)</label>
                                                        <input type="file" name="picture" id="picture" class="form-control" onchange="previewImage(event)">
                                                        @error('picture')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 d-flex align-items-center">
                                                    <img id="avatar" class="avatar" src="{{ asset($user->picture ??  'https://via.placeholder.com/150') }}" alt="User Picture">
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Update User</button>
                                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
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
