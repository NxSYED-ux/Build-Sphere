@extends('layouts.guest')

@section('title', 'Organization')

@push('styles')
    <style>
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
        }

        .image-upload-frame {
            position: relative;
            width: 130px;
            height: 130px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px dashed #ced4da;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            transition: border 0.3s ease;
            margin: 10px auto;
        }

        .image-upload-frame:hover {
            border-color: #0d6efd;
        }

        .image-upload-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-btn {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background-color: #0d6efd;
            border: none;
            border-radius: 50%;
            padding: 6px;
            cursor: pointer;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-btn input[type="file"] {
            display: none;
        }

        .upload-btn i {
            font-size: 16px;
        }
    </style>
@endpush

@section('content')
    <x-error-success-model />
    <section class="content my-3 mx-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="box" style="overflow-x: auto;">
                        <div class="container mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="mb-0">Sign Up</h4>
                                <a href="{{ route('admin-login-index') }}" class="btn btn-secondary">Login</a>
                            </div>
                            <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                <div class="card-body " >

                                    <form action="{{ route('owner-signUp-register') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row rounded p-2" style="background-color: #f8f9fa;">
                                            <h5 class="mb-2">Organization</h5>
                                            <div class="col-lg-10">
                                                <div class="row">

                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="name">Organization Name</label>
                                                            <span class="required__field">*</span><br>
                                                            <div class="position-relative">
                                                                <input type="text" name="org_name" id="org_name" class="form-control @error('org_name') is-invalid @enderror" value="{{ old('org_name') }}" maxlength="50" placeholder="Organization Name" required>
                                                                <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('org_name')
                                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <!--  -->
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <label for="org_country" >Country</label>
                                                            <select class="form-select" id="org_country" name="org_country">
                                                                <option value="" selected>Select Country</option>
                                                            </select>
                                                            @error('org_country')
                                                            <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <!--  -->
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <label for="org_province" >Province</label>
                                                            <select class="form-select" id="org_province" name="org_province">
                                                                <option value="" selected>Select Province</option>
                                                            </select>
                                                            @error('org_province')
                                                            <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <!--  -->
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <label for="org_city" >City</label>
                                                            <select class="form-select" id="org_city" name="org_city">
                                                                <option value="" selected>Select Province</option>
                                                            </select>
                                                            @error('org_city')
                                                            <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!--  -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="org_location">Location</label>
                                                            <div class="position-relative">
                                                                <input type="text" name="org_location" id="org_location" class="form-control @error('org_location') is-invalid @enderror" value="{{ old('org_location') }}" maxlength="100" placeholder="Enter Location">
                                                                <i class='bx bxs-edit-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('org_location')
                                                            <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <!--  -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="org_postal_code">Postal Code</label>
                                                            <div class="position-relative">
                                                                <input type="text" name="org_postal_code" id="org_postal_code" class="form-control @error('org_postal_code') is-invalid @enderror" value="{{ old('org_postal_code') }}" maxlength="100" placeholder="Enter Postal Code">
                                                                <i class='bx bx-current-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('org_postal_code')
                                                            <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4 col-lg-2 d-flex justify-content-center align-items-center">
                                                <div class="image-upload-frame">
                                                    <img id="org_avatar" src="{{ old('organization_picture') ? asset(old('organization_picture')) : asset('img/organization_placeholder.png') }}" alt="Organization Picture">
                                                    <label class="upload-btn" for="organization_picture">
                                                        <i class="bx bx-camera"></i>
                                                        <input type="file" name="organization_picture" id="organization_picture" accept="image/*" onchange="previewImage(event, 'org_avatar')">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row rounded p-2 my-2" style="background-color: #f8f9fa;">
                                            <h5 class="mb-2">Owner Details</h5>
                                            <div class="col-lg-10">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="name">Owner Name</label>
                                                            <span class="required__field">*</span><br>
                                                            <div class="position-relative">
                                                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" maxlength="50" placeholder="Owner Name" required>
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

                                                    <!-- Email Input -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="email">Email</label>
                                                            <span class="required__field">*</span><br>
                                                            <div class="position-relative">
                                                                <input type="email" name="email" id="email"
                                                                       class="form-control @error('email') is-invalid @enderror"
                                                                       value="{{ old('email') }}" placeholder="Email" maxlength="50" required>
                                                                <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- OTP & Verify -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="email_otp">Verify Email</label>
                                                            <div class="d-flex">
                                                                <input type="text" name="otp" id="email_otp" class="form-control me-2"
                                                                       placeholder="Enter OTP" maxlength="6" @error('otp') is-invalid @enderror" value="{{ old('otp') }}">
                                                                <button type="button" id="verifyEmailBtn" class="btn btn-outline-primary">Verify</button>
                                                            </div>
                                                            <small class="text-muted" id="otpStatusText">OTP will be sent to your email</small>
                                                            @error('otp')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Password -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="password">Password</label>
                                                            <span class="required__field">*</span><br>
                                                            <div class="position-relative">
                                                                <input type="password" name="password" id="password"
                                                                       class="form-control @error('password') is-invalid @enderror"
                                                                       value="{{ old('password') }}" placeholder="Password" minlength="8" maxlength="100" required>
                                                                <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('password')
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
                                                            <select name="gender" id="gender" class="form-select" required>
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
                                                            <label for="date_of_birth">Date of Birth</label>
                                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', date('Y-m-d')) }}">
                                                            @error('date_of_birth')
                                                            <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!--  -->
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <label for="country" >Country</label>
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
                                                            <label for="province" >Province</label>
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
                                                            <label for="city" >City</label>
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

                                                    <!--  -->
                                                    <div class="col-sm-12 col-md-12 col-lg-12">
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
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4 col-lg-2 d-flex justify-content-center align-items-center">
                                                <div class="image-upload-frame">
                                                    <img id="avatar" src="{{ old('picture') ? asset(old('picture')) : asset('img/placeholder-profile.png') }}" alt="User Picture">
                                                    <label class="upload-btn" for="picture">
                                                        <i class="bx bx-camera"></i>
                                                        <input type="file" name="picture" id="picture" accept="image/*" onchange="previewImage(event, 'avatar')">
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary w-100">Submit</button>
                                            </div>
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
@endsection

@push('scripts')

    <script>
        function previewImage(event, targetId) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById(targetId);
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

    <script>
        document.getElementById('verifyEmailBtn').addEventListener('click', function () {
            const email = document.getElementById('email').value;
            const statusText = document.getElementById('otpStatusText');

            if (!email) {
                alert('Please enter an email first.');
                return;
            }

            fetch("{{ route('send_signup_otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusText.innerText = data.message;
                        statusText.classList.remove('text-muted');
                        statusText.classList.add('text-success');
                    } else {
                        statusText.innerText = data.message || 'Failed to send OTP.';
                        statusText.classList.remove('text-muted');
                        statusText.classList.add('text-danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusText.innerText = 'Something went wrong.';
                    statusText.classList.remove('text-muted');
                    statusText.classList.add('text-danger');
                });
        });
    </script>

    <!-- Country/Province/City dropdowns  -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countrySelect = document.getElementById('country');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            const org_countrySelect = document.getElementById('org_country');
            const org_provinceSelect = document.getElementById('org_province');
            const org_citySelect = document.getElementById('org_city');

            const dropdownData = @json($dropdownData);
            const org_dropdownData = @json($dropdownData);

            // Populate Personal Country Dropdown
            dropdownData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.values[0]?.value_name || 'Unnamed Country';
                option.dataset.id = country.id;
                option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                countrySelect.appendChild(option);
            });

            // Handle Personal Country Change
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

            // Handle Personal Province Change
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

            // Populate Organization Country Dropdown
            org_dropdownData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.values[0]?.value_name || 'Unnamed Country';
                option.dataset.id = country.id;
                option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                org_countrySelect.appendChild(option);
            });

            // Handle Org Country Change
            org_countrySelect.addEventListener('change', function () {
                org_provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';
                org_citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const org_selectedCountryId = this.options[this.selectedIndex]?.dataset.id;
                const org_selectedCountry = org_dropdownData.find(c => c.id == org_selectedCountryId);

                if (org_selectedCountry) {
                    org_selectedCountry.values.forEach(province => {
                        province.childs.forEach(childProvince => {
                            const option = document.createElement('option');
                            option.value = childProvince.value_name;
                            option.dataset.id = childProvince.id;
                            option.textContent = childProvince.value_name;
                            org_provinceSelect.appendChild(option);
                        });
                    });
                }
            });

            // Handle Org Province Change
            org_provinceSelect.addEventListener('change', function () {
                org_citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const org_selectedCountryId = org_countrySelect.options[org_countrySelect.selectedIndex]?.dataset.id;
                const org_selectedCountry = org_dropdownData.find(c => c.id == org_selectedCountryId);

                if (org_selectedCountry) {
                    const org_selectedProvinceId = this.options[this.selectedIndex]?.dataset.id;
                    const org_selectedProvince = org_selectedCountry.values
                        .flatMap(province => province.childs)
                        .find(p => p.id == org_selectedProvinceId);

                    if (org_selectedProvince) {
                        org_selectedProvince.childs.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.value_name;
                            option.dataset.id = city.id;
                            option.textContent = city.value_name;
                            org_citySelect.appendChild(option);
                        });
                    }
                }
            });
        });
    </script>



@endpush
