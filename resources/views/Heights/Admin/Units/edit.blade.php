@extends('layouts.app')

@section('title', 'Edit Unit')

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

    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('units.index'), 'label' => 'Units'],
            ['url' => '', 'label' => 'Edit Unit']
        ]"
    />
    <x-Admin.side-navbar :openSections="['Buildings', 'Units']" />
    <x-error-success-model />

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">Edit Unit</h4>
                                    <a href="{{ route('units.index') }}" class="btn btn-secondary">Go Back</a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " >

                                        <form action="{{ route('units.update', $unit->id) }}" method="POST" enctype="multipart/form-data">
                                        @method('PUT')
                                            <input type="hidden" name="updated_at" value="{{ $unit->updated_at }}">
                                            <div class="row my-0 py-0">
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="unit_name">Unit Name</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="text" name="unit_name" id="unit_name" class="form-control @error('unit_name') is-invalid @enderror" value="{{ old('unit_name', $unit->unit_name) }}" maxlength="50" placeholder="User Name" required>
                                                        @error('unit_name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="unit_type">Unit Type</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="unit_type" id="unit_type" class="form-select" required>
                                                            @foreach($unitTypes as $value)
                                                                <option value="{{ $value->value_name }}" {{ old('unit_type', $unit->unit_type) == $value->value_name ? 'selected' : '' }}>
                                                                    {{ $value->value_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('unit_type')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="sale_or_rent">Sale or Rent</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="sale_or_rent" id="sale_or_rent" class="form-select" required>
                                                            <option value="Sale" {{ old('sale_or_rent', $unit->sale_or_rent) == 'Sale' ? 'selected' : '' }}>Sale</option>
                                                            <option value="Rent" {{ old('sale_or_rent', $unit->sale_or_rent) == 'Rent' ? 'selected' : '' }}>Rent</option>
                                                            <option value="Not Available" {{ old('sale_or_rent', $unit->sale_or_rent) == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                                        </select>
                                                        @error('sale_or_rent')
                                                        <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="availability_status">Availability Status</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="availability_status" id="availability_status" class="form-select" required>
                                                            <option value="Available" {{ old('availability_status', $unit->availability_status) == 'Available' ? 'selected' : '' }}>Available</option>
                                                            <option value="Rented" {{ old('availability_status', $unit->availability_status) == 'Rented' ? 'selected' : '' }}>Rented</option>
                                                            <option value="Sold" {{ old('availability_status', $unit->availability_status) == 'Sold' ? 'selected' : '' }}>Sold</option>
                                                            <option value="Not Available" {{ old('availability_status', $unit->availability_status) == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                                        </select>
                                                        @error('availability_status')
                                                        <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="price">Price</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $unit->price) }}" placeholder="Enter unit price" required>
                                                        @error('price')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                 <!--  -->
                                                 <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="area">Area</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="number" name="area" id="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area', $unit->area) }}" placeholder="1234" required>
                                                        @error('area')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="description">Description</label>
                                                        <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $unit->description) }}" maxlength="50" placeholder="Description">
                                                        @error('description')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Organization Dropdown -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="organization_id">Organization</label>
                                                        <span class="required__field">*</span><br>
                                                        <select class="form-select" id="organization_id" name="organization_id" required>
                                                            <option value="" disabled selected>Select Organization</option>
                                                            @foreach($organizations as $organization)
                                                                <option value="{{ $organization->id }}" {{ old('organization_id', $unit->organization_id) == $organization->id ? 'selected' : '' }}>
                                                                    {{ $organization->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('organization_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Building Dropdown -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="building_id">Building</label>
                                                        <span class="required__field">*</span><br>
                                                        <select class="form-select" id="building_id" name="building_id" required>
                                                            <option value="" disabled selected>Select Building</option>
                                                        </select>
                                                        @error('building_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Level Dropdown -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="level_id">Level</label>
                                                        <span class="required__field">*</span><br>
                                                        <select class="form-select" id="level_id" name="level_id" required>
                                                            <option value="" disabled selected>Select Level</option>
                                                        </select>
                                                        @error('level_id')
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
                                                            <option value="Approved" {{ old('status', $unit->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="Rejected" {{ old('status', $unit->status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                        </select>
                                                        @error('status')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>

                                            <h4>
                                                <button class="btn w-100 text-start" style="background-color: #D3D3D3;" type="button" data-bs-toggle="collapse" data-bs-target="#pictures" aria-expanded="false" aria-controls="pictures">
                                                    Pictures <i class="fa fa-chevron-down"></i>
                                                </button>
                                            </h4>
                                            <div id="pictures" class="collapse show collapsible-section">
                                                <div class="row">
                                                    <!-- Unit Images -->
                                                    <div class="ccol-lg-4 col-md-4 col-sm-12">
                                                        <label for="imageInput" class="form-label">Unit Pictures</label>
                                                        <input type="file" id="imageInput" name="unit_pictures[]" class="form-control" multiple>
                                                        @error('unit_pictures.*')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-4 col-md-8 col-sm-12">
                                                        <label class="form-label">Already Uploaded</label>
                                                        <div id="imagePreview" class="">
                                                            @foreach ($unit->pictures as $image)
                                                                <div class="image-thumbnail">
                                                                    <img src="{{ asset($image->file_path) }}" class="thumbnail-image" alt="Uploaded Image">
                                                                    <button type="button" class="thumbnail-remove" data-image-id="{{ $image->id }}" onclick="removeExistingImage('{{ $image->id }}')">&times;</button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-12 col-sm-12">
                                                        <label class="form-label">New Uploads</label>
                                                        <div id="imageThumbnails" class="">
                                                            <!-- Image thumbnails will be inserted here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 mt-3">
                                                <button type="submit" class="btn btn-primary w-100">Save</button>
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

    <!-- Collapse Script -->
    <script>
        function toggleIcon(collapseElement) {
            collapseElement.addEventListener('show.bs.collapse', function () {
                this.previousElementSibling.querySelector('i').classList.remove('fa-chevron-right');
                this.previousElementSibling.querySelector('i').classList.add('fa-chevron-down');
            });

            collapseElement.addEventListener('hide.bs.collapse', function () {
                this.previousElementSibling.querySelector('i').classList.remove('fa-chevron-down');
                this.previousElementSibling.querySelector('i').classList.add('fa-chevron-right');
            });
        }

        // Select all collapsible elements with a common class
        document.querySelectorAll('.collapsible-section').forEach(function (element) {
            toggleIcon(element);
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
                    fetch(`{{ route('units.remove_picture', ':id') }}`.replace(':id', imageId), {
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Function to fetch buildings based on selected organization
            function fetchBuildings(organizationId, selectedBuilding = null, selectedLevel = null) {
                if (organizationId) {
                    fetch(`{{ route('organizations.buildings', ':id') }}`.replace(':id', organizationId), {
                        method: "GET",
                        headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
                    })
                        .then(response => response.json())
                        .then(data => {
                            const buildingSelect = document.getElementById("building_id");
                            buildingSelect.innerHTML = `<option value="" disabled selected>Select Building</option>`;

                            // Populate the buildings dropdown
                            Object.entries(data.buildings).forEach(([key, value]) => {
                                const option = document.createElement("option");
                                option.value = key;
                                option.textContent = value;
                                buildingSelect.appendChild(option);
                            });

                            // Preselect the building if available
                            if (selectedBuilding) {
                                buildingSelect.value = selectedBuilding;
                            }

                            // Reset and populate levels based on the selected building
                            document.getElementById("level_id").innerHTML = `<option value="" disabled selected>Select Level</option>`;
                            if (selectedBuilding) {
                                fetchLevels(selectedBuilding, selectedLevel);
                            }
                        })
                        .catch(error => console.error("Error fetching buildings:", error));
                } else {
                    document.getElementById("building_id").innerHTML = `<option value="" disabled selected>Select Building</option>`;
                    document.getElementById("level_id").innerHTML = `<option value="" disabled selected>Select Level</option>`;
                }
            }

            // Function to fetch levels based on selected building
            function fetchLevels(buildingId, selectedLevel = null) {
                if (buildingId) {
                    fetch(`{{ route('buildings.levels', ':id') }}`.replace(':id', buildingId), {
                        method: "GET",
                        headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
                    })
                        .then(response => response.json())
                        .then(data => {
                            const levelSelect = document.getElementById("level_id");
                            levelSelect.innerHTML = `<option value="" disabled selected>Select Level</option>`;

                            // Populate the levels dropdown
                            Object.entries(data.levels).forEach(([key, value]) => {
                                const option = document.createElement("option");
                                option.value = key;
                                option.textContent = value;
                                levelSelect.appendChild(option);
                            });

                            // Preselect the level if available
                            if (selectedLevel) {
                                levelSelect.value = selectedLevel;
                            }
                        })
                        .catch(error => console.error("Error fetching levels:", error));
                } else {
                    document.getElementById("level_id").innerHTML = `<option value="" disabled selected>Select Level</option>`;
                }
            }

            // Get the initial values from PHP variables
            const organizationId = `{{ old('organization_id', $unit->organization_id) }}`;
            const selectedBuilding = `{{ old('building_id', $unit->level->building_id) }}`;
            const selectedLevel = `{{ old('level_id', $unit->level_id) }}`;

            // Fetch buildings and levels based on the initial selected values
            fetchBuildings(organizationId, selectedBuilding, selectedLevel);

            // Trigger building fetch when organization is changed
            document.getElementById("organization_id").addEventListener("change", function () {
                fetchBuildings(this.value);
            });

            // Trigger level fetch when building is changed
            document.getElementById("building_id").addEventListener("change", function () {
                fetchLevels(this.value);
            });
        });

    </script>












@endpush
