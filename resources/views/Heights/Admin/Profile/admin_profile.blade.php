@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .bxs-camera{
            color: #008CFF;
        }

        .remove_picture_button{
            height: 40px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #EC5252;
            color: #EC5252;
            background-color: white;
        }

        .remove_picture_button:hover{
            border: 1px solid #EC5252;
            color: #fff;
            background-color: #EC5252;
        }

        .change_password_button{
            /*width: 166px;*/
            height: 40px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #008CFF;
            color: white;
            background-color: #008CFF;
        }

        .change_password_button:hover{
            border: 1px solid #008CFF;
            color: #008CFF;
            background-color: #fff;
        }

        .submit-btn{
            background-color: #008CFF;
            color: white;
        }

        .form label{
            font-weight: bold;
        }

        .form input:not(.form-check-input):not(.is-invalid),
        select:not(.is-invalid),
        textarea:not(.is-invalid) {
            padding: 8px !important;
            border-radius: 5px !important;
        }

        .form i{
            color: var(--input-icon-color) !important;
        }

        /* Model Windows */
        .modal-content{
            background: var(--modal-header-bg);
            color: var(--modal-text);
        }

        .modal-header {
            background: var(--modal-header-bg);
            color: var(--modal-text);
        }

        .modal-title {
            font-weight: bold;
            width: 100%;
            text-align: center;
        }

        .modal-body {
            background: var(--modal-bg);
            color: var(--modal-text);
        }

        .modal-footer {
            background: var(--modal-bg);
            border-top: 1px solid var(--modal-border);
        }

        .btn-close {
            filter: invert(var(--invert, 0));
        }

    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Profile']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Dashboard']" />
    <x-error-success-model />


    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container py-2">

                                <div class="pt-1 position-relative ">
                                    <div class="d-flex align-items-center gap-3 flex-nowrap">
                                        <!-- Profile Image -->
                                        <div class="profile-img-container position-relative d-inline-block">
                                            <img id="profile-image"
                                                 src="{{ Auth::user() && Auth::user()->picture ? asset(Auth::user()->picture) : asset('img/avatar.png') }}"
                                                 alt="User Image"
                                                 class="profile-img img-fluid rounded-circle shadow-sm border"
                                                 style="width: 100px; height: 100px; object-fit: cover;">

                                            <!-- Upload Icon -->
                                            <label for="image-upload"
                                                   class="image-upload-icon position-absolute bg-white shadow d-flex align-items-center justify-content-center rounded-circle"
                                                   style="width: 30px; height: 30px; bottom: 0; right: -5px;">
                                                <i class='bx bxs-camera'></i>
                                            </label>
                                            <input type="file" id="image-upload" class="d-none" accept="image/*">
                                        </div>

                                        <!-- User Info -->
                                        <div class="user-info flex-grow-1">
                                            <h3 class="mb-1 fw-bold">{{ $user->name }}</h3>
                                            <p class="text mb-1 fw-semibold">
                                                {{ $user->role->name }}
                                                <span id="userStatus" class="rounded-circle d-inline-block mt-2 mx-2"
                                                      style="width: 10px; height: 10px; background-color: green;">
                                                </span>
                                            </p>
                                        </div>

                                        <!-- Buttons for Large Screens -->
                                        <div class="d-none d-lg-flex flex-row align-items-center ms-auto" style="margin-right: 5px;">
                                            <button class="btn me-2 remove_picture_button" @if(empty(Auth::user()->picture)) style="display: none;" @endif>Remove Profile</button>
                                            <button class="btn change_password_button" >Change Password</button>
                                        </div>
                                    </div>

                                    <!-- Buttons for Small Screens -->
                                    <div class="d-flex d-lg-none flex-column flex-sm-row justify-content-center mt-3">
                                        <button class="btn mb-2 mb-sm-0 me-sm-2 remove_picture_button" @if(empty(Auth::user()->picture)) style="display: none;" @endif> Remove Profile </button>

                                        <button class="btn change_password_button" >Change Password</button>
                                    </div>
                                </div>

                                <!-- Separator Line -->
                                <hr class="mt-4 mb-4 border-secondary" style="border-color: #5F5F5F; !important">



                                <form method="POST" class="form" action="{{ route('admin.profile.update', $user->id) }}" id="profile-form">
                                    @method('PUT')

                                    <input type="hidden" name="updated_at" value="{{ $user->updated_at }}">
                                    <!-- Personal Information -->
                                    <div class="profile-card">

                                        <div class="row">

                                            <div class="col-lg-4 col-md-4 col-12 mb-3">
                                                <label for="name">Name</label>
                                                <div class="position-relative">
                                                <input type="text" name="name" id="name" class="form-control pe-5 custom-input  "
                                                    value="{{ old('name', $user->name) }}" maxlength="50" placeholder="User Name" required>
                                                    <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 mb-3">
                                                <label for="email">Email</label>
                                                <div class="position-relative">
                                                    <input type="text" name="email" id="email"
                                                           class="form-control custom-input pe-5 @error('email') is-invalid @enderror"
                                                           value="{{ old('email', $user->email) }}" maxlength="50"
                                                           placeholder="Email" required readonly>
                                                    <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 mb-3">
                                                    <label for="cnic">CNIC</label>
                                                <div class="position-relative">
                                                    <input type="text" name="cnic" id="cnic" class="form-control pe-5 custom-input @error('cnic') is-invalid @enderror"
                                                        value="{{ old('cnic', $user->cnic) }}" maxlength="15" placeholder="12345-1234567-1" required>
                                                    <i class='bx bxs-id-card input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                    @error('cnic')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-12 mb-3">
                                                    <label for="contact">Phone Number</label>
                                                    <div class="position-relative">
                                                        <input type="text" name="phone_no" id="contact"
                                                           class="form-control pe-5 custom-input @error('phone_no') is-invalid @enderror"
                                                           value="{{ old('phone_no', $user->phone_no) }}" placeholder="0312-3456789" maxlength="14">
                                                        <i class='bx bxs-mobile input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                    </div>
                                                    @error('phone_no')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-6 mb-3">
                                                    <label for="date_of_birth">Date of Birth</label>
                                                    <input type="date" name="date_of_birth" id="date_of_birth"
                                                        class="form-control custom-input @error('date_of_birth') is-invalid @enderror"
                                                        value="{{ old('date_of_birth', isset($user->date_of_birth) ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                                                    @error('date_of_birth')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-6 mb-3">
                                                    <label for="gender">Gender</label>
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

                                        <!--  -->
                                        <div class="row">
                                            <!--  -->
                                            <div class="col-lg-8 col-md-4 col-sm-12 col-12 mb-3">


                                                <label for="location">Location</label>
                                                <div class="position-relative">
                                                    <input type="text" name="address[location]" id="location" class="form-control pe-5 custom-input @error('location') is-invalid @enderror" value="{{ old('location', $user->address->location ) }}" maxlength="100" placeholder="Enter Location">
                                                    <i class='bx bxs-edit-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('location')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <!--  -->
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
                                                <label for="postal_code">Postal Code</label>
                                                <div class="position-relative">
                                                    <input type="text" name="address[postal_code]" id="postal_code" class="form-control pe-5 custom-input @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $user->address->postal_code ) }}" maxlength="100" placeholder="Enter Postal Code">
                                                    <i class='bx bx-current-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('postal_code')
                                                <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                @enderror
                                            </div>

                                            <!--  -->
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
                                                <div class="position-relative">
                                                    <label for="country" >Country</label>
                                                    <select class="form-select custom-input" id="country" name="address[country]">
                                                        <option value="" selected>Select Country</option>
                                                    </select>
                                                    @error('country')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--  -->
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
                                                <div class=" position-relative">
                                                    <label for="province" >Province</label>
                                                    <select class="form-select custom-input" id="province" name="address[province]">
                                                        <option value="" selected>Select Province</option>
                                                    </select>
                                                    @error('province')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!--  -->
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
                                                <div class=" position-relative">
                                                    <label for="city" >City</label>
                                                    <select class="form-select custom-input" id="city" name="address[city]">
                                                        <option value="" selected>Select Province</option>
                                                    </select>
                                                    @error('customer_city')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 submit-btn mt-3" form="profile-form">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Create Dropdwon Type Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changePasswordForm" method="POST" action="{{ route('admin.profile.password.update') }}">
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Old Password</label>
                            <span class="required__field">*</span><br>
                            <input type="password" class="form-control @error('old_password') is-invalid @enderror" id="old_password" name="old_password" value="{{ old('old_password') }}" maxlength="30" placeholder="Old Password" required>
                            @error('old_password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <span class="required__field">*</span><br>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" value="{{ old('new_password') }}" maxlength="30" placeholder="New Password" required>
                            @error('new_password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <span class="required__field">*</span><br>
                            <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" value="{{ old('confirm_password') }}" maxlength="30" placeholder="Confirm Password" required>
                            @error('confirm_password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            $('.change_password_button').on('click', function (e) {
                e.preventDefault();
                $('#changePasswordModal').modal('show');
            });
        });
    </script>

    <!-- Remove Profile -->
    <script>
        document.querySelectorAll('.remove_picture_button').forEach(button => {
            button.addEventListener('click', function () {
                if (!confirm("Are you sure you want to remove your profile picture?")) {
                    return;
                }

                fetch("{{ route('admin.profile.picture.delete') }}", {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('profile-image').src = "{{ asset('img/avatar.png') }}";
                            document.querySelectorAll(".remove_picture_button").forEach(button => {
                                button.style.display = "none";
                            });

                        } else {
                            alert("Error: " + (data.message || "Unable to remove profile picture."));
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Something went wrong. Please try again.");
                    });
            });
        });
    </script>

    <!-- Update Profile -->
    <script>
        $(document).ready(function () {
            $('#image-upload').on('change', function (event) {
                let file = event.target.files[0];

                if (file) {
                    let formData = new FormData();
                    formData.append('picture', file);
                    formData.append('_method', 'PUT'); // Required for Laravel PUT request

                    $.ajax({
                        url: "{{ route('admin.profile.picture.update') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        beforeSend: function () {
                            $('#image-upload').prop('disabled', true);
                        },
                        success: function (response) {
                            if (response.success) {
                                let reader = new FileReader();
                                reader.onload = function (e) {
                                    $('#profile-image').attr('src', e.target.result);
                                    // document.getElementById("remove_picture_button").style.display = "block";
                                    document.querySelectorAll(".remove_picture_button").forEach(button => {
                                        button.style.display = "block";
                                    });

                                };
                                reader.readAsDataURL(file);
                            } else if (response.error) {
                                alert(response.error);
                            }
                        },
                        error: function (xhr) {
                            let errorMessage = "Error updating profile picture. Please try again.";

                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            } else if (xhr.responseText) {
                                errorMessage = xhr.responseText;
                            }

                            alert(errorMessage);
                        },
                        complete: function () {
                            $('#image-upload').prop('disabled', false); // Re-enable input after upload
                        }
                    });
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

            const dropdownData = @json($user->dropdownData);
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
