@extends('layouts.app')

@section('title', 'Edit Building')

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

    <style>
        /* Apply Styles to Elements Inside .document-container */
        .document-container {
            background-color: var(--bg-color);
            color: var(--text-color);
            padding: 15px;
            border-radius: 8px;
        }

        .document-container .bg-light {
            background-color: var(--bg-color) !important;
        }

        .document-container .form-control,
        .document-container .form-select {
            background-color: var(--bg-color);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .document-container .form-control::placeholder {
            color: var(--border-color);
        }

        .document-container .btn {
            background-color: var(--btn-bg);
            color: var(--btn-text);
        }

        .document-container .btn:hover {
            opacity: 0.9;
        }

        .document-container .btn-danger {
            background-color: var(--btn-bg);
            color: var(--btn-text);
            border-color: var(--btn-bg);
        }

    </style>
@endpush

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('buildings.index'), 'label' => 'Buildings'],
            ['url' => '', 'label' => 'Edit Building']
        ]"
    />
    <!--  -->
    <x-Admin.side-navbar :openSections="['Buildings', 'Building']" />
    <x-error-success-model />

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid ">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">Edit Building</h4>
                                    <a href="{{ route('buildings.index') }}" class="btn btn-secondary">Go Back</a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body">
                                        <form action="{{ route('buildings.update') }}" method="POST" enctype="multipart/form-data">
                                            @method('PUT')
                                            @csrf

                                            <input type="hidden" name="id" value="{{ $building->id }}">
                                            <input type="hidden" name="updated_at" value="{{ $building->updated_at }}">

                                            <div class="row mb-3">
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $building->name) }}" maxlength="50" placeholder="Organization Name" required>
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="building_type">Building Type</label>
                                                        <span class="required__field">*</span><br>
                                                        <select name="building_type" id="building_type" class="form-select" required>
                                                            @foreach($buildingTypes as $value)
                                                                <option value="{{ $value->value_name }}" {{ old('building_type', $building->building_type) == $value->value_name ? 'selected' : '' }}>
                                                                    {{ $value->value_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('building_type')
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
                                                            <option value="Approved" {{ old('status', $building->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="Under Review" {{ old('status', $building->status) == 'Under Review' ? 'selected' : '' }}>Under Review</option>
                                                            <option value="Rejected" {{ old('status', $building->status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                            <option value="Under Process" {{ old('status', $building->status) == 'Under Process' ? 'selected' : '' }}>Under Process</option>
                                                            <option value="Reapproved" {{ old('status', $building->status) == 'Reapproved' ? 'selected' : '' }}>Reapproved</option>
                                                        </select>
                                                        @error('status')
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
                                                        <input type="number" name="area" id="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area', $building->area) }}" placeholder="1234" required>
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
                                                        <label for="organization_id">Organization</label>
                                                        <span class="required__field">*</span><br>
                                                        <select class="form-select" id="organization_id" name="organization_id" value="{{ old('organization_id', $building->organization_id) }}" required>
                                                            <option value="" disabled {{ old('organization_id') === null ? 'selected' : '' }}>Select Organization</option>
                                                            @foreach($organizations as $organization)
                                                                <option value="{{ $organization->id }}" {{ old('organization_id', $building->organization_id) == $organization->id ? 'selected' : '' }}>
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

                                                <!-- <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="remarks">Remarks</label>
                                                        <input type="text" name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" value="{{ old('remarks', $building->remarks) }}" placeholder="Remarks"  maxlength="50">
                                                        @error('remarks')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>  -->
                                                <!-- Construction Year -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="construction_year">Construction Year</label>
                                                        <input type="number" name="construction_year" id="construction_year" class="form-control @error('construction_year') is-invalid @enderror" value="{{ old('construction_year', $building->construction_year) }}" placeholder="2024">
                                                        @error('construction_year')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
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

                                            <h4>
                                                <button class="btn w-100 text-start" style="background-color: #D3D3D3;" type="button" data-bs-toggle="collapse" data-bs-target="#pictures" aria-expanded="false" aria-controls="pictures">
                                                    Pictures <i class="fa fa-chevron-down"></i>
                                                </button>
                                            </h4>
                                            <div id="pictures" class="collapse show collapsible-section mb-3">
                                                <div class="row">
                                                    <!-- Organization Images -->
                                                    <div class="ccol-lg-4 col-md-4 col-sm-12">
                                                        <label for="imageInput" class="form-label">Building Pictures</label>
                                                        <input type="file" id="imageInput" name="building_pictures[]" class="form-control" multiple>
                                                        @error('building_pictures.*')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-4 col-md-8 col-sm-12">
                                                        <label class="form-label">Already Uploaded</label>
                                                        <div id="imagePreview" class="">
                                                            @foreach ($building->pictures as $image)
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

                                            <!-- Documents Section -->
                                            <h4>
                                                <button class="btn w-100 text-start" style="background-color: #D3D3D3;" type="button" data-bs-toggle="collapse" data-bs-target="#documents" aria-expanded="false" aria-controls="documents">
                                                    Documents <i class="fa fa-chevron-right"></i>
                                                </button>
                                            </h4>
                                            <div id="documents" class="collapse p-2 collapsible-section document-container">
                                                @foreach ($documentTypes as $type)
                                                    <div class="mb-3 border p-2" data-type="{{ $type }}" data-file-count="{{ $building->documents->where('document_type', $type)->count() }}" data-max-files="5">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <label class="form-label"><b><i><u style="text-decoration-thickness: 2px;">{{ ucfirst($type) }}</u></i></b></label>
                                                            </div>
                                                            <div class="col-md-8">
                                                                @foreach ($building->documents->where('document_type', $type) as $file)
                                                                    <div class="row mt-1">
                                                                        <div class="col-md-3">
                                                                            <label><b>Issue Date:</b> {{ isset($file->issue_date) ? \Carbon\Carbon::parse($file->issue_date)->format('d-m-Y') : '' }}</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label><b>Expiry Date:</b> {{ isset($file->expiry_date) ? \Carbon\Carbon::parse($file->expiry_date)->format('d-m-Y') : '' }}</label>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="d-flex align-items-center mb-2">
                                                                                <i class="fa fa-file me-2"></i>
                                                                                <a href="{{ asset($file->file_path) }}" target="_blank" class="text-decoration-none me-2">{{ basename($file->file_path) }}</a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <button type="button" class="btn btn-danger btn-sm ms-2 remove-file" data-type="{{ $type }}" data-file-id="{{ $file->id }}" onclick="removeExistingFile('{{ $file->id }}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove File">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-warning btn-sm ms-2 update-file-button" data-id="{{ $file->id }}" onclick="updateExistingFile('{{ $file->id }}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Update File">
                                                                                <i class="fa fa-pencil"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="col-md-2">
                                                                <small>{{ $building->documents->where('document_type', $type)->count() }} / 5 files</small>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label for="issue_date_{{ $type }}" class="form-label">Issue Date</label>
                                                                <input type="date" id="issue_date_{{ $type }}" class="form-control" name="documents[{{ $type }}][issue_date]">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="expiry_date_{{ $type }}" class="form-label">Expiry Date</label>
                                                                <input type="date" id="expiry_date_{{ $type }}" class="form-control" name="documents[{{ $type }}][expiry_date]">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">File</label>
                                                                <input type="file" name="documents[{{ $type }}][files]" class="form-control">
                                                                <input type="hidden" name="documents[{{ $type }}][type]" value="{{ $type }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 mt-3">
                                                <button type="submit" class="btn btn-primary w-100">Update</button>
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

    <!-- Edit File Modal -->
    <div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFileModalLabel">Edit Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- The edit form will be loaded here via AJAX -->
                    <form id="editFileForm" action="" method="POST" enctype="multipart/form-data">
                        @method('PUT')

                        <div class="mb-3">
                            <label for="document_type" class="form-label">Document Type</label>
                            <span class="required__field">*</span><br>
                            <select name="document_type" id="edit_document_type" class="form-control form-select" required>
                                <option value="" selected>Select Type</option>
                                @foreach($documentTypes as $id => $value_name)
                                    <option value="{{ $value_name }}" {{ old('document_type') == $value_name ? 'selected' : '' }}>{{ $value_name }}</option>
                                @endforeach
                            </select>
                            @error('document_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="issue_date" class="form-label">Issue Date</label>
                            <input type="date" class="form-control" id="edit_issue_date" name="issue_date">
                            @error('issue_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="edit_expiry_date" name="expiry_date"  >
                            @error('expiry_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">File <br> (max size: 5048mb)</label>
                            <input type="file" class="form-control" id="edit_file" name="file" max-size="5048">
                            @error('file')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update File</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <!-- Remove or delete building pictures script -->
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
                    fetch(`{{ route('buildings.remove_picture', ':id') }}`.replace(':id', imageId), {
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

            function updateImageInput() {
                const dt = new DataTransfer();
                selectedImages.forEach(file => {
                    dt.items.add(file);
                });
                imageInput.files = dt.files;
            }

            document.querySelector('form').addEventListener('submit', function(event) {
                updateImageInput();
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
            const userCountry = "{{ $building->address->country }}";
            const userProvince = "{{ $building->address->province }}";
            const userCity = "{{ $building->address->city }}";

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

    <!-- Delete document script -->
    <script>
        function removeExistingFile(fileId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Are you sure you want to remove this document?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('building_documents.removeDocument', ':id') }}`.replace(':id', fileId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the image from the DOM
                            document.querySelector(`button[data-file-id="${fileId}"]`).closest('.row').remove();
                        } else {
                            alert('Failed to remove file.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }
    </script>

    <!-- Document max control script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const documentContainer = document.getElementById('documents');

            documentContainer.addEventListener('change', function (event) {
                if (event.target.type === 'file') {
                    const fileInput = event.target;
                    const parentDiv = fileInput.closest('div[data-type]');
                    const maxFiles = parseInt(fileInput.getAttribute('max-files'), 10);

                    if (fileInput.files.length > maxFiles) {
                        alert(`You can only upload a maximum of ${maxFiles} files.`);
                        fileInput.value = ""; // Clear the selected files
                    }
                }
            });
        });
    </script>

    <!-- Update file script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const updateFileButtons = document.getElementsByClassName("update-file-button");

            Array.from(updateFileButtons).forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();
                    console.log("Update file button clicked");

                    const id = this.getAttribute("data-id");
                    console.log("File ID:", id);

                    fetch(`{{ route('building_document.edit', ':id') }}`.replace(':id', id), {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Accept": "application/json"
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log("AJAX request successful");

                            if (data.success) {
                                console.log("Data retrieved:", data.document);

                                var documentData = data.document;

                                // Populate the form fields with the retrieved document data
                                document.getElementById("edit_document_type").value = documentData.document_type;
                                document.getElementById("edit_issue_date").value = documentData.issue_date
                                    ? new Date(documentData.issue_date).toISOString().slice(0, 16)
                                    : '';
                                document.getElementById("edit_expiry_date").value = documentData.expiry_date
                                    ? new Date(documentData.expiry_date).toISOString().slice(0, 16)
                                    : '';
                                document.getElementById("edit_file").value = ''; // Clear previous file input

                                // Set the form action URL
                                document.getElementById("editFileForm").setAttribute("action",
                                    `{{ route('building_document.update', ':id') }}`.replace(':id', id)
                                );

                                // Show the modal using Bootstrap
                                const editFileModal = new bootstrap.Modal(document.getElementById("editFileModal"));
                                editFileModal.show();
                            } else {
                                console.log('Error: Could not retrieve document data.');
                            }
                        })
                        .catch(() => {
                            console.log('An error occurred while retrieving the data.');
                        });
                });
            });
        });
    </script>



@endpush
