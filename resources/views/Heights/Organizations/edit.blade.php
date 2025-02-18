@extends('layouts.app')

@section('title', 'Edit Organization')

@push('styles')
    <style>
        body { 
        }
        #main { 
            margin-top: 45px;
        }  

        .image-thumbnail {
            display: inline-block;
            margin: 5px;
            position: relative;
        } 
        .thumbnail-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .thumbnail-remove {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.7);
            border: none;
            cursor: pointer;
        } 
        
    </style>
@endpush 

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false"/>
    <!--  --> 
    <x-Admin.side-navbar :openSections="['Organizations']" />
    <x-error-success-model /> 
 
    <div id="main"> 
        <section class="content-header pt-2"> 
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{url('admin_dashboard')}}">Dashboard</a></li> 
                    <li class="breadcrumb-item"><a href="{{ route('organizations.index') }}">Organizations</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Edit Organization</a></li>
                </ol>
            </nav> 
        </section>  

        <section class="content content-top  my-3 mx-2">
            <div class="container-fluid ">
                <div class="row"> 
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">Edit Organization</h4>
                                    <a href="{{ route('organizations.index') }}" class="btn btn-secondary">Go Back</a>
                                </div>  
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body"> 
                                        <form action="{{ route('organizations.update', $organization->id) }}" method="POST" enctype="multipart/form-data"> 
                                            @method('PUT')

                                            <div class="row mb-3">
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $organization->name) }}" maxlength="50" placeholder="Organization Name" required>
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div> 
                                                
                                                <!-- Organization -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="owner_id">Owner</label>
                                                        <span class="required__field">*</span><br>
                                                        <select class="form-select" id="owner_id" name="owner_id" required>
                                                            <option value="" disabled {{ old('owner_id') === null ? 'selected' : '' }}>Select Organization</option>
                                                            @foreach($owners as $id => $name)
                                                                <option value="{{ $id }}" {{ old('owner_id', $organization->owner_id) == $id ? 'selected' : '' }}>
                                                                    {{ $name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('owner_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="status">Status</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="status" id="status" class="form-select" required>
                                                            <option value="enable" {{ old('status', $organization->status) == 'enable' ? 'selected' : '' }}>Enable</option>
                                                            <option value="disable" {{ old('status', $organization->status) == 'disable' ? 'selected' : '' }}>Disable</option>
                                                            <option value="block" {{ old('status', $organization->status) == 'block' ? 'selected' : '' }}>Block</option>
                                                        </select>
                                                        @error('status')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="membership_start_date" class="form-label" >Membership Start Date</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="date" class="form-control" id="membership_start_date" name="membership_start_date" value="{{ old('membership_start_date', isset($organization->membership_start_date) ? \Carbon\Carbon::parse($organization->membership_start_date)->format('Y-m-d') : '') }}" required>
                                                        @error('membership_start_date')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="membership_end_date" class="form-label" >Membership End Date</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="date" class="form-control" id="membership_end_date" name="membership_end_date" value="{{ old('membership_end_date', isset($organization->membership_end_date) ? \Carbon\Carbon::parse($organization->membership_end_date)->format('Y-m-d') : '') }}" required>
                                                        @error('membership_end_date')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div> 

                                                <!--  -->
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="country">Country</label> 
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
                                                        <label for="province">Province</label> 
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
                                                        <label for="city">City</label> 
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
                                                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $organization->address->location ) }}" maxlength="100" placeholder="Enter Location"> 
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
                                                        <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $organization->address->postal_code ) }}" maxlength="100" placeholder="Enter Postal Code"> 
                                                        @error('postal_code')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div> 

                                            <div class="row">
                                                <!-- Organization Images -->
                                                <div class="ccol-lg-4 col-md-4 col-sm-12">
                                                    <label for="imageInput" class="form-label">Organization Pictures</label>
                                                    <input type="file" id="imageInput" name="organization_pictures[]" class="form-control" multiple>
                                                    @error('organization_pictures.*')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-sm-12">
                                                    <label class="form-label">Already Uploaded</label>
                                                    <div id="imagePreview" class="">
                                                        @foreach ($organization->pictures as $image)
                                                            <div class="image-thumbnail">
                                                                <img src="{{ asset($image->file_path) }}" class="thumbnail-image" alt="Uploaded Image">
                                                                <button type="button" class="thumbnail-remove" data-image-id="{{ $image->id }}" onclick="removeExistingImage('{{ $image->id }}')">&times;</button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <label class="form-label">New Uploads</label>
                                                    <div id="imageThumbnails" class="">
                                                        <!-- Image thumbnails will be inserted here -->
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Update Organization</button>
                                            <a href="{{ route('organizations.index') }}" class="btn btn-secondary">Cancel</a>
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
            var passwordField = this;
            var passwordHelp = document.getElementById('passwordHelp');

            if (passwordField.value.length === 0) {
                passwordField.classList.remove('is-invalid');
                passwordHelp.textContent = '';
            } 
            else if (passwordField.value.length < 8) {
                passwordField.classList.add('is-invalid');
                passwordHelp.textContent = 'Password must be at least 8 characters long.';
            } 
             else {
                passwordField.classList.remove('is-invalid');
                passwordHelp.textContent = '';
            }
        });
    </script>

    <!-- Cnic Validation -->
    <script>
        document.getElementById('cnic').addEventListener('input', function (e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,4})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] + (x[4] ? '-' + x[4] : '') : '');
        });
    </script> 

    <!-- Remove or delete organization pictures script -->
    <script> 

        function removeExistingImage(imageId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Are you sure you want to remove this image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) { 
                    fetch(`{{ route('organizations.remove_picture', ':id') }}`.replace(':id', imageId), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the image from the DOM
                            document.querySelector(`button[data-image-id="${imageId}"]`).parentElement.remove();
                            Swal.fire({
                                title: 'Success!',
                                text: 'Deleted successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else { 
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to remove image.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }

    </script> 

    <!-- Organization Pictures script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedImages = [];
            let currentIndex = 0;

            const imageInput = document.getElementById('imageInput');

            imageInput.addEventListener('change', handleImageSelection);

            function handleImageSelection(event) {
                const files = Array.from(event.target.files);
                selectedImages = [...selectedImages, ...files];  
                currentIndex = selectedImages.length - files.length; 
                renderThumbnails();
                showImage();
            }

            function renderThumbnails() {
                const container = document.getElementById('imageThumbnails');
                container.innerHTML = '';

                selectedImages.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.classList.add('image-thumbnail');

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('thumbnail-image');
                        img.style.width = '100px';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';

                        const removeButton = document.createElement('button');
                        removeButton.innerHTML = '&#10006;';
                        removeButton.classList.add('thumbnail-remove');
                        removeButton.onclick = function() {
                            removeImage(index);
                        };

                        div.appendChild(img);
                        div.appendChild(removeButton);
                        container.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });

                updateImageInput();
            }

            function showImage() {
                const img = document.getElementById('currentImage');
                if (selectedImages.length > 0) {
                    const file = selectedImages[currentIndex];
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        img.classList.add('active');
                        document.getElementById('imageCounter').textContent = `${currentIndex + 1} / ${selectedImages.length}`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    img.classList.remove('active');
                }
            } 

            function removeImage(index) {
                selectedImages.splice(index, 1);
                currentIndex = Math.min(currentIndex, selectedImages.length - 1);
                renderThumbnails();
                showImage();
            }

            function removeSelectedImage() {
                if (selectedImages.length > 0) {
                    selectedImages.splice(currentIndex, 1);
                    currentIndex = Math.min(currentIndex, selectedImages.length - 1);
                    renderThumbnails();
                    showImage();
                }
            }

            function updateImageInput() {
                const dt = new DataTransfer();
                selectedImages.forEach(file => {
                    dt.items.add(file);
                });
                imageInput.files = dt.files;
            }

            document.querySelector('form').addEventListener('submit', function(event) {
                updateImageInput(); // Ensure the imageInput is updated with the selected images before form submission
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
            const userCountry = "{{ $organization->address->country }}";
            const userProvince = "{{ $organization->address->province }}";
            const userCity = "{{ $organization->address->city }}";

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