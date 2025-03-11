@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .profile-card {
            background: var(--profile-card-bg-color);
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            padding: 20px;
        }

        .profile-img-container {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .profile-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .user-info {
            white-space: nowrap;  /* Prevents text from breaking */
            overflow: hidden;
            text-overflow: ellipsis;
            /*max-width: auto;  !* Adjust as needed *!*/
        }

        .image-upload-icon {
            bottom: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
        }

        .image-upload-icon:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .remove-img-btn {
            top: 5px;
            right: 5px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            opacity: 0.8;
        }

        .remove-img-btn:hover {
            opacity: 1;
        }

        /* Fix Change Password Button */
        .change-password-btn {
            position: absolute;
            top: 15px;
            right: 15px;
        }

        /* Responsive Adjustments */
        @media (max-width: 576px) {
            .profile-card {
                flex-direction: column;
                align-items: center;
                text-align: left;
            }

            .profile-img-container {
                width: 70px;
                height: 70px;
            }

            .user-info {
                flex-grow: 1;
                max-width: 100%;  /* Allow full width on small screens */
            }

            /* Move button to bottom-right */
            .change-password-btn {
                position: relative;
                align-self: flex-end;
                margin-top: 10px;
            }
        }





        /*.profile-card {*/
        /*    background: var(--profile-card-bg-color);*/
        /*    border-radius: 12px;*/
        /*    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);*/
        /*    margin-bottom: 1.5rem;*/
        /*    padding: 20px;*/
        /*}*/

        /*.profile-img {*/
        /*    width: 105px;*/
        /*    height: 105px;*/
        /*    border-radius: 50%;*/
        /*    object-fit: cover;*/
        /*    border: 2px solid #ddd;*/
        /*}*/

        /*.profile-header {*/
        /*    display: flex;*/
        /*    align-items: center;*/
        /*    gap: 15px;*/
        /*    margin-bottom: 20px;*/
        /*}*/

        .profile-header h3 {
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        .profile-header p {
            margin: 0;
            color: #777;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--main-text-color);
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .info-row div {
            flex: 1;
            min-width: 150px;
            margin-bottom: 10px;
        }

        .info-row span {
            color: #888;
            font-size: 14px;
        }

        label {
            color: #888;
            font-size: 14px;
        }

        .info-row p {
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        .edit-btn {
            background-color: #008cff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 6px 12px;
            text-decoration: none;

        }

        .edit-btn:hover {
            border: 1px solid #008cff;
            background-color: #fff;
            color: #008cff;
        }

        /* Make input fields look like paragraphs */
        .custom-input {
            /*background: none;*/
            background-color: var(--main-background-color);
            border: none;
            padding: 0;
            /*padding-left: 2px;*/
            font-size: inherit;
            line-height: inherit;
            width: 100%;
            display: block;
            color: inherit;
            /*transition: background-color 0.5s ease;*/
        }

        /* Hover effect */
        .custom-input:hover {
            background-color: #f0f0f0;
        }

        /* Focus styling to remove default focus outline */
        .custom-input:focus {
            outline: none;
            box-shadow: none;
            background-color: transparent;
        }

        /* Optional: Style the placeholder text */
        .custom-input::placeholder {
            color: #6c757d;
            opacity: 1;
        }

        .profile-img-container {
            position: relative;
            width: 105px;
            height: 105px;
            overflow: visible; /* Allow icon to overflow outside the image */
        }

        .profile-img {
            width: 105px;
            height: 105px;
            object-fit: cover;
        }

        .image-upload-icon {
            cursor: pointer;
            background-color: #E8E9EB;
            border-radius: 50%;
            padding: 2px;
            position: absolute;
            bottom: 0;
            right: 0;
            z-index: 1;
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
                            <div class="container py-4">

                                <div class="profile-card    p-3 position-relative">
                                    <div class="d-flex align-items-center flex-nowrap gap-3 flex-grow-1">
                                        <!-- Profile Image -->
                                        <div class="profile-img-container position-relative">
                                            <img id="profile-image"
                                                 src="{{ Auth::user() && Auth::user()->picture ? asset(Auth::user()->picture) : asset('img/avatar.png') }}"
                                                 alt="User Image"
                                                 class="profile-img img-fluid rounded-circle shadow-sm border">

                                            <!-- Upload Icon -->
                                            <label for="image-upload" class="image-upload-icon position-absolute p-2">
                                                <i class='bx bxs-camera'></i>
                                            </label>
                                            <input type="file" id="image-upload" class="d-none" accept="image/*">

                                            <!-- Remove Image Button -->
                                            <button id="remove-image" class="btn btn-danger btn-sm position-absolute remove-img-btn">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>

                                        <!-- User Info -->
                                        <div class="user-info ">
                                            <h3 class="mb-1">{{ $user->name }}</h3>
                                            <p class="text-success mb-1">{{ $user->role->name }}</p>
                                            <p class="text-muted mb-0">{{ $user->address->country }}</p>
                                        </div>
                                    </div>

                                    <!-- Change Password Button -->
                                    <div class="d-flex flex-column flex-sm-row justify-content-end w-100">

{{--                                        <a href="#" class="btn btn-primary mt-sm-0 mt-3" id="change_password_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Change">Password</a>--}}
                                        <button class="btn btn-primary mt-sm-0 mt-3" id="change_password_button" data-bs-toggle="tooltip" data-bs-placement="top" title="Change">Password</button>
                                    </div>
                                </div>


                                <form method="POST" action="{{ route('admin.profile.update', $user->id) }}" id="profile-form">
                                    @method('PUT')
                                    <!-- Personal Information -->
                                    <div class="profile-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="section-title">Personal</div>
                                            <button type="submit" class="btn btn-primary" form="profile-form">Save</button>
                                        </div>

                                            <div class="row">
                                                <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                    <label for="name">Name</label>
                                                    <input type="text" name="name" id="name" class="form-control custom-input @error('name') is-invalid @enderror"
                                                        value="{{ old('name', $user->name) }}" maxlength="50" placeholder="User Name" required>
                                                    @error('name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                    <label>Email</label>
                                                    <p><b>{{ $user->email }}</b></p>
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                    <label for="cnic">CNIC</label>
                                                    <input type="text" name="cnic" id="cnic" class="form-control custom-input @error('cnic') is-invalid @enderror"
                                                        value="{{ old('cnic', $user->cnic) }}" maxlength="15" placeholder="12345-1234567-1" required>
                                                    @error('cnic')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                    <label for="date_of_birth">Date of Birth</label>
                                                    <input type="date" name="date_of_birth" id="date_of_birth"
                                                        class="form-control custom-input @error('date_of_birth') is-invalid @enderror"
                                                        value="{{ old('date_of_birth', isset($user->date_of_birth) ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                                                    @error('date_of_birth')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                    <label for="contact">Phone Number</label>
                                                    <input type="text" name="phone_no" id="contact"
                                                        class="form-control custom-input @error('phone_no') is-invalid @enderror"
                                                        value="{{ old('phone_no', $user->phone_no) }}" placeholder="0312-3456789" maxlength="14">
                                                    @error('phone_no')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                    <label>User Role</label>
                                                    <p><b>{{ $user->role->name }}</b></p>
                                                </div>
                                            </div>

                                    </div>

                                    <!-- Address Information -->
                                    <div class="profile-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="section-title">Address</div>
    {{--                                        <button type="submit" class="btn btn-primary" form="profile-form">Save</button>--}}
                                        </div>
                                        <!--  -->
                                        <div class="row">
                                        <!--  -->
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-2">
                                            <div class="form-group mb-3 position-relative">
                                                <label for="country" class="form-label">Country</label>
                                                <select class="form-select custom-input" id="country" name="address[country]">
                                                    <option value="" selected>Select Country</option>
                                                </select>
{{--                                                <i class="fa fa-chevron-down position-absolute    translate-middle-y " style="top: 40px; right: 10px;"></i>--}}
                                                @error('country')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!--  -->
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-2">
                                            <div class="form-group mb-3 position-relative">
                                                <label for="province" class="form-label">Province</label>
                                                <select class="form-select custom-input" id="province" name="address[province]">
                                                    <option value="" selected>Select Province</option>
                                                </select>
{{--                                                <i class="fa fa-chevron-down position-absolute    translate-middle-y " style="top: 40px; right: 10px;"></i>--}}
                                                @error('province')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!--  -->
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-2">
                                            <div class="form-group mb-3 position-relative">
                                                <label for="city" class="form-label">City</label>
                                                <select class="form-select custom-input" id="city" name="address[city]">
                                                    <option value="" selected>Select Province</option>
                                                </select>
{{--                                                <i class="fa fa-chevron-down position-absolute    translate-middle-y " style="top: 40px; right: 10px;"></i>--}}
                                                @error('customer_city')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!--  -->
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-2">
                                            <div class="form-group mb-3">
                                                <label for="location">Location</label>
                                                <input type="text" name="address[location]" id="location" class="form-control custom-input @error('location') is-invalid @enderror" value="{{ old('location', $user->address->location ) }}" maxlength="100" placeholder="Enter Location">
                                                @error('location')
                                                <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!--  -->
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                                                <div class="form-group mb-3">
                                                    <label for="postal_code">Postal Code</label>
                                                    <input type="text" name="address[postal_code]" id="postal_code" class="form-control custom-input @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $user->address->postal_code ) }}" maxlength="100" placeholder="Enter Postal Code">
                                                    @error('postal_code')
                                                    <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                    @enderror
                                                </div>
                                            </div>
                                    </div>
                                    </div>
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
            $('#change_password_button').on('click', function (e) {
                e.preventDefault();
                $('#changePasswordModal').modal('show');
            });
        });
    </script>

    <script>
        document.getElementById('remove-image').addEventListener('click', function() {
            document.getElementById('profile-image').src = "{{ asset('img/avatar.png') }}";
        });
    </script>


{{--    <!-- Country/Province/City dropdowns  -->--}}
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', () => {--}}
{{--            const countrySelect = document.getElementById('country');--}}
{{--            const provinceSelect = document.getElementById('province');--}}
{{--            const citySelect = document.getElementById('city');--}}

{{--            const dropdownData = @json($user->dropdownData);--}}
{{--            const userCountry = "{{ $user->address->country }}";--}}
{{--            const userProvince = "{{ $user->address->province }}";--}}
{{--            const userCity = "{{ $user->address->city }}";--}}

{{--            // Populate Country Dropdown--}}
{{--            dropdownData.forEach(country => {--}}
{{--                const option = document.createElement('option');--}}
{{--                option.value = country.values[0]?.value_name || 'Unnamed Country'; // Use value_name for the value--}}
{{--                option.dataset.id = country.id; // Store ID in a data attribute--}}
{{--                option.textContent = country.values[0]?.value_name || 'Unnamed Country';--}}
{{--                if (option.value === userCountry) {--}}
{{--                    option.selected = true; // Pre-select user's country--}}
{{--                }--}}
{{--                countrySelect.appendChild(option);--}}
{{--            });--}}

{{--            // Populate Provinces--}}
{{--            function populateProvinces() {--}}
{{--                provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';--}}
{{--                citySelect.innerHTML = '<option value="" selected>Select City</option>';--}}

{{--                const selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id; // Retrieve ID from data attribute--}}
{{--                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);--}}

{{--                if (selectedCountry) {--}}
{{--                    selectedCountry.values.forEach(province => {--}}
{{--                        province.childs.forEach(childProvince => {--}}
{{--                            const option = document.createElement('option');--}}
{{--                            option.value = childProvince.value_name; // Use value_name for the value--}}
{{--                            option.dataset.id = childProvince.id; // Store ID in a data attribute--}}
{{--                            option.textContent = childProvince.value_name;--}}
{{--                            if (option.value === userProvince) {--}}
{{--                                option.selected = true; // Pre-select user's province--}}
{{--                            }--}}
{{--                            provinceSelect.appendChild(option);--}}
{{--                        });--}}
{{--                    });--}}
{{--                }--}}
{{--            }--}}

{{--            // Populate Cities--}}
{{--            function populateCities() {--}}
{{--                citySelect.innerHTML = '<option value="" selected>Select City</option>';--}}

{{--                const selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id; // Retrieve ID from data attribute--}}
{{--                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);--}}

{{--                if (selectedCountry) {--}}
{{--                    const selectedProvinceId = provinceSelect.options[provinceSelect.selectedIndex]?.dataset.id; // Retrieve ID from data attribute--}}
{{--                    const selectedProvince = selectedCountry.values--}}
{{--                        .flatMap(province => province.childs)--}}
{{--                        .find(p => p.id == selectedProvinceId);--}}

{{--                    if (selectedProvince) {--}}
{{--                        selectedProvince.childs.forEach(city => {--}}
{{--                            const option = document.createElement('option');--}}
{{--                            option.value = city.value_name; // Use value_name for the value--}}
{{--                            option.dataset.id = city.id; // Store ID in a data attribute--}}
{{--                            option.textContent = city.value_name;--}}
{{--                            if (option.value === userCity) {--}}
{{--                                option.selected = true; // Pre-select user's city--}}
{{--                            }--}}
{{--                            citySelect.appendChild(option);--}}
{{--                        });--}}
{{--                    }--}}
{{--                }--}}
{{--            }--}}

{{--            // Event Listeners--}}
{{--            countrySelect.addEventListener('change', populateProvinces);--}}
{{--            provinceSelect.addEventListener('change', populateCities);--}}

{{--            // Prepopulate Provinces and Cities--}}
{{--            if (userCountry) {--}}
{{--                populateProvinces();--}}
{{--            }--}}
{{--            if (userProvince) {--}}
{{--                populateCities();--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}


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
                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
                         },
                         beforeSend: function () {
                             $('#image-upload').prop('disabled', true); // Disable input during upload
                         },
                         success: function (response) {
                             if (response.success) {
                                 // Use FileReader to preview the image after success
                                 let reader = new FileReader();
                                 reader.onload = function (e) {
                                     $('#profile-image').attr('src', e.target.result);
                                 };
                                 reader.readAsDataURL(file); // Convert file to base64 URL

                                 alert("Profile picture updated successfully!");
                             } else if (response.error) {
                                 alert(response.error); // Show validation or error messages
                             }
                         },
                         error: function (xhr) {
                             let errorMessage = "Error updating profile picture. Please try again.";

                             if (xhr.responseJSON && xhr.responseJSON.error) {
                                 errorMessage = xhr.responseJSON.error; // Show Laravel validation error
                             } else if (xhr.responseText) {
                                 errorMessage = xhr.responseText; // Show any other server error
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
@endpush
