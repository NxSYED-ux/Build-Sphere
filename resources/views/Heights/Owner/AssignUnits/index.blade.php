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

        /* Center the input and preview */
        .image-input-container {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .image-input-container .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: center;
            border: 2px dashed var(--sidenavbar-text-color);
            border-radius: 10px;
            padding: 15px;
            height: 160px;
            background-color: var(--main-background-color);
            margin-top: 10px;
            overflow-y: auto;
            text-align: center;
            position: relative;
        }

        .image-preview p {
            flex-basis: 100%;
            text-align: center;
            font-size: 16px;
            color: #666;
        }

        /* Ensure images are displayed properly */
        .image-input-container .image-item {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border: 1px solid #ddd;
            overflow: hidden;
        }

        .image-input-container .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Always show upload button */
        .image-preview .upload-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #6c63ff;
            color: #fff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .image-preview .upload-btn:hover {
            background-color: #5752d3;
        }

        /* Hide default file input */
        .image-input-container input[type="file"] {
            display: none;
        }

        .image-input-container .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(255, 0, 0, 0.8);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .image-input-container .remove-btn:hover {
            background-color: red;
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
                            <div class="col-md-6" >
                                <div class="card shadow p-3 mb-2 bg-body rounded border-none" style="border: none;">
                                    <div class="row">

                                        <!--  -->
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group mb-2">
                                                <label for="user_id">User</label>
                                                <select name="userId" id="user_id" class="form-select" required>
                                                    <option value="">Select User</option>
                                                    @forelse($users as $id => $name)
                                                        <option value="{{ $id }}" {{ old('userId', $selectedUserId) == $id ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @empty
                                                        <option value="">No user found</option>
                                                    @endforelse
                                                </select>
                                                @error('userId')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="user_name">Name</label>
                                                <span class="required__field">*</span><br>
                                                <div class="position-relative">
                                                    <input type="text" name="user_name" id="user_name" class="form-control @error('user_name') is-invalid @enderror" value="{{ old('user_name') }}" maxlength="50" placeholder="User Name" required>
                                                    <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_name')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="user_email">Email</label>
                                                <span class="required__field">*</span><br>
                                                <div class="position-relative">
                                                    <input type="email" name="user_email" id="user_email" class="form-control @error('user_email') is-invalid @enderror" value="{{ old('user_email') }}" placeholder="Email"  maxlength="50" required>
                                                    <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_email')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="user_contact">Phone no:</label><br>
                                                <div class="position-relative">
                                                    <input type="text" name="user_phone_no" id="user_contact" value="{{ old('user_phone_no') }}"
                                                           class="form-control contact" placeholder="0312-3456789" maxlength="14">
                                                    <i class='bx bxs-mobile input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_phone_no')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="user_cnic">CNIC</label>
                                                <span id="cnic_status"></span><br>
                                                <div class="position-relative">
                                                    <input type="text" name="user_cnic" id="user_cnic" class="form-control @error('user_cnic') is-invalid @enderror"
                                                           value="{{ old('user_cnic') }}" maxlength="15" placeholder="12345-1234567-1">
                                                    <i class='bx bxs-id-card input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_cnic')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="user_gender">Gender</label>
                                                <span class="required__field">*</span><br>
                                                <select name="user_gender" id="user_gender" class="form-select" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male" {{ old('user_gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ old('user_gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                    <option value="Other" {{ old('user_gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('user_gender')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="user_date_of_birth">Date of Birth</label>
                                                <input type="date" class="form-control" id="user_date_of_birth" name="user_date_of_birth" value="{{ old('user_date_of_birth', date('Y-m-d')) }}">
                                                @error('user_date_of_birth')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!--  -->
                                    <div class="row">
                                        <!--  -->
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group mb-2">
                                                <label for="user_location">Location</label>
                                                <div class="position-relative">
                                                    <input type="text" name="user_location" id="user_location" class="form-control @error('user_location') is-invalid @enderror" value="{{ old('user_location') }}" maxlength="100" placeholder="Enter Location">
                                                    <i class='bx bxs-edit-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_location')
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="country" class="form-label">Country</label>
                                                <select class="form-select" id="country" name="user_country">
                                                    <option value="" selected>Select Country</option>
                                                </select>
                                                @error('user_country')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="province" class="form-label">Province</label>
                                                <select class="form-select" id="province" name="user_province">
                                                    <option value="" selected>Select Province</option>
                                                </select>
                                                @error('user_province')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="city" class="form-label">City</label>
                                                <select class="form-select" id="city" name="user_city">
                                                    <option value="" selected>Select Province</option>
                                                </select>
                                                @error('user_city')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="user_postal_code" class="form-label">Postal Code</label>
                                                <div class="position-relative">
                                                    <input type="text" name="user_postal_code" id="user_postal_code" class="form-control @error('user_postal_code') is-invalid @enderror" value="{{ old('user_postal_code') }}" maxlength="100" placeholder="Enter Postal Code">
                                                    <i class='bx bx-current-location input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                </div>
                                                @error('user_postal_code')
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!--  -->
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="user_picture">Picture</label>
                                                <input type="file" name="user_picture" id="user_picture" class="form-control" accept="image/*" onchange="previewImage(event)">
                                            </div>
                                            @error('user_picture')
                                            <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-6 d-flex justify-content-center align-items-center">
                                            <img id="avatar" class="avatar" src="{{ old('user_picture') ? asset(old('user_picture')) : asset('img/placeholder-profile.png') }}" alt="User Picture">
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="card shadow p-3 mb-2 bg-body rounded border-none" style="border: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="building_id">Buildings</label>
                                                <select name="building_id" id="building_id" class="form-select" required>
                                                    <option value="" disabled selected>Select Building</option>
                                                    @forelse($buildings as $building)
                                                        <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                                            {{ $building->name }}
                                                        </option>
                                                        @empty
                                                            <option value="">No building found</option>
                                                        @endforelse
                                                </select>
                                                @error('building_id')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="unit_id">Units</label>
                                                <input type="hidden" name="unitName" id="unit_name">
                                                <select name="unitId" id="unit_id" class="form-select" required>
                                                    <option value="">Select Unit</option>
                                                    @forelse($units as $unit)
                                                        <option value="{{ $unit->id }}" {{ old('unitId', $selectedUnitId) == $unit->id ? 'selected' : '' }}>
                                                            {{ $unit->unit_name }}
                                                        </option>
                                                    @empty
                                                        <option value="">No unit found</option>
                                                    @endforelse
                                                </select>
                                                @error('unitId')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Second Column: Unit Image -->
                                        <div class="col-md-6 text-center">
                                            <img id="unitImage" src="{{ asset('img/buildings/Apartment_1.jpg') }}"
                                                 alt="Unit Image" class="img-fluid rounded shadow w-100"
                                                 style="height: 150px; object-fit: cover;">
                                        </div>

                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="row">
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
                                </div>



                                <div class="card shadow p-3 w-100 mb-2 bg-body rounded border-none" style="border: none;">
                                    <div class="row">

                                        <!--  -->
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-2">
                                                <label for="contract_type">Contract Type <span class="required__field">*</span><br></label>
                                                <select name="type" id="contract_type" class="form-select" required>
                                                    <option value="">Select Type</option>
                                                    <option value="Sold" {{ old('type') == 'Sold' ? 'selected' : '' }}>Sale</option>
                                                    <option value="Rented" {{ old('type') == 'Rented' ? 'selected' : '' }}>Rent</option>
                                                </select>
                                                @error('type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group mb-3">
                                                <label for="price">Contract Price <span class="required__field">*</span><br></label>
                                                <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="Enter unit price" required>
                                                @error('price')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Rent Dates (Only Show for "Rented") -->
                                        <div class="col-sm-12 col-md-6 col-lg-6 rent-fields d-none">
                                            <div class="form-group mb-2">
                                                <label for="rent_start_date">Rent Start Date <span class="required__field indicator d-none">*</span><br></label>
                                                <input type="date" class="form-control" id="rent_start_date" name="rentStartDate" value="{{ old('rentStartDate', date('Y-m-d')) }}">
                                                @error('rentStartDate')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-6 rent-fields d-none">
                                            <div class="form-group mb-2">
                                                <label for="rent_end_date">Rent End Date <span class="required__field indicator d-none">*</span><br></label>
                                                <input type="date" class="form-control" id="rent_end_date" name="rentEndDate" value="{{ old('rentEndDate', date('Y-m-d')) }}">
                                                @error('rentEndDate')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Purchase Date (Only Show for "Sold") -->
                                        <div class="col-sm-12 col-md-12 col-lg-12 purchase-field d-none">
                                            <div class="form-group mb-2">
                                                <label for="purchase_date">Purchase Date <span class="required__field indicator d-none">*</span><br></label>
                                                <input type="date" class="form-control" id="purchase_date" name="purchaseDate" value="{{ old('purchaseDate', date('Y-m-d')) }}">
                                                @error('purchaseDate')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="image-input-container mt-1">
                                                <input type="file" id="image-input" name="pictures[]" accept="image/png, image/jpeg, image/jpg, image/gif" multiple hidden>
                                                @error('pictures')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
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

                        <div class="row">
                            <div class="col-6 mt-1">
                                <button class="btn btn-outline-primary w-100">Close</button>
                            </div>
                            <div class="col-6  mt-1">
                                <button type="submit" class="btn btn-primary w-100">Save</button>
                            </div>
                        </div>

                    </form>
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
                        "Accept": "application/json"
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


    <!-- Unit Detail script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const unitSelect = document.getElementById("unit_id");

            function fetchUnitDetails(unitId) {
                if (!unitId) return; // Prevent fetching if no unit is selected

                fetch(`{{ route('owner.units.details', ':id') }}`.replace(':id', unitId), {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
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
                        document.getElementById('building_id').value = unit.building_id;
                        document.getElementById('unit_name').value = unit.unit_name;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
            }

            // Fetch details on dropdown change
            unitSelect.addEventListener("change", function () {
                fetchUnitDetails(this.value);
            });

            // Fetch details on page load if a unit is already selected
            if (unitSelect.value) {
                fetchUnitDetails(unitSelect.value);
            }
        });
    </script>

    <!-- Sold/ Rented Contract type -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const contractType = document.getElementById("contract_type");
            const rentFields = document.querySelectorAll(".rent-fields");
            const purchaseField = document.querySelector(".purchase-field");

            // Helper to update the required indicator visibility in the label for a given input.
            function updateIndicator(input, show) {
                let label = document.querySelector("label[for='" + input.id + "']");
                if (label) {
                    let indicator = label.querySelector(".indicator");
                    if (indicator) {
                        if (show) {
                            indicator.classList.remove("d-none");
                        } else {
                            indicator.classList.add("d-none");
                        }
                    }
                }
            }

            // Clears input value and removes its required attribute and hides its indicator.
            function clearInput(element) {
                const input = element.querySelector("input");
                if (input) {
                    input.value = "";
                    input.removeAttribute("required");
                    updateIndicator(input, false);
                }
            }

            // Set required attribute on input and show its required indicator.
            function setRequired(input) {
                if (input) {
                    input.setAttribute("required", "");
                    updateIndicator(input, true);
                }
            }

            function toggleFields() {
                if (contractType.value === "Rented") {
                    // Show rent fields, set them as required, and hide & clear purchase field.
                    rentFields.forEach(field => {
                        field.classList.remove("d-none");
                        const input = field.querySelector("input");
                        if (input) {
                            setRequired(input);
                        }
                    });
                    purchaseField.classList.add("d-none");
                    clearInput(purchaseField);
                } else if (contractType.value === "Sold") {
                    // Show purchase field, set it as required, and hide & clear rent fields.
                    purchaseField.classList.remove("d-none");
                    const purchaseInput = purchaseField.querySelector("input");
                    if (purchaseInput) {
                        setRequired(purchaseInput);
                    }
                    rentFields.forEach(field => {
                        field.classList.add("d-none");
                        clearInput(field);
                    });
                } else {
                    // No valid selection: hide both sections and clear their values and required attributes.
                    rentFields.forEach(field => {
                        field.classList.add("d-none");
                        clearInput(field);
                    });
                    purchaseField.classList.add("d-none");
                    clearInput(purchaseField);
                }
            }

            contractType.addEventListener("change", toggleFields);
            toggleFields(); // Initialize on page load
        });
    </script>




@endpush
