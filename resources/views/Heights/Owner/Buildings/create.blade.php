@extends('layouts.app')

@section('title', 'Add Building')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }
        .padding-y {
            padding-top: .5rem !important;
            padding-bottom: .5rem !important;
            transition: padding .1s;
        }
        .padding-y:hover {
            padding-top: .3rem !important;
            padding-bottom: .7rem !important;
        }
        #add_button {
            width: 45px;
            height: 45px;
            margin-right: 10px;
            background-color: #adadad;
            color: black;
            border: 1px solid grey;
            font-size: 25px;
            font-weight: bold;
            align-items: center;
            justify-content: center;
        }

        /* Center the input and preview */
        .image-input-container {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Style the label button */
        .image-input-container .custom-file-label {
            display: inline-block;
            background-color: #6c63ff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        .image-input-container .custom-file-label:hover {
            background-color: #5752d3;
        }

        /* Image preview area */
        .image-input-container .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            border: 2px dashed #6c63ff;
            border-radius: 10px;
            padding: 15px;
            background-color: #fff;
            margin-top: 10px;
        }

        /* No images selected message */
        .image-input-container .image-preview p {
            flex-basis: 100%;
            text-align: center;
            font-size: 16px;
            color: #666;
        }

        /* Style each image item */
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
            object-fit: cover; /* Change to contain if the image isn't showing as expected */
        }

        /* Remove button for each image */
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
                ['url' => url('owner_manager_dashboard'), 'label' => 'Dashboard'],
                ['url' => route('owner.buildings.index'), 'label' => 'Buildings'],
                ['url' => '', 'label' => 'Create Building']
            ]"
    />
    <x-Owner.side-navbar :openSections="['Buildings', 'Building']" />
    <x-error-success-model />

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">Add New Building</h4>
                                    <a href="{{ route('owner.buildings.index') }}" class="btn btn-secondary">Go Back</a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " >

                                        <form action="{{ route('owner.buildings.store') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                                    <div class="row my-0 py-0">

                                                        <!-- Name -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="name">Name</label>
                                                                <span class="required__field">*</span><br>
                                                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" maxlength="50" placeholder="Building Name" required>
                                                                @error('name')
                                                                <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <div class="form-group mb-3">
                                                                <label for="building_type">Building Type</label>
                                                                <span class="required__field">*</span><br>
                                                                <select name="building_type" id="building_type" class="form-select" required>
                                                                    <option value="" selected>Select Type</option>
                                                                    @foreach($buildingTypes as $value)
                                                                        <option value="{{ $value->value_name }}" {{ old('building_type') == $value->value_name ? 'selected' : '' }}>
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


                                                        <!-- Area -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="area">Area</label>
                                                                <span class="required__field">*</span><br>
                                                                <input type="number" name="area" id="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area') }}" placeholder="1234" required>
                                                                @error('area')
                                                                <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!-- Construction Year -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="construction_year">Construction Year</label>
                                                                <input type="number" name="construction_year" id="construction_year" class="form-control @error('construction_year') is-invalid @enderror" value="{{ old('construction_year') }}" placeholder="2024">
                                                                @error('construction_year')
                                                                <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!-- Country -->
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
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
                                                        <!-- Province -->
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
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

                                                        <!-- City -->
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <div class="form-group mb-3">
                                                                <label for="city">City</label>
                                                                <select class="form-select" id="city" name="city">
                                                                    <option value="" selected>Select City</option>
                                                                </select>
                                                                @error('city')
                                                                <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!--  -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="location">Location</label>
                                                                <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" maxlength="100" placeholder="Enter Location">
                                                                @error('location')
                                                                <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!--  -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="postal_code">Postal Code</label>
                                                                <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}" maxlength="100" placeholder="Enter Postal Code">
                                                                @error('postal_code')
                                                                <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--  -->

                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                                                    <div class="image-input-container mt-4">
                                                        <label for="image-input" class="custom-file-label">
                                                            <span>Choose Images</span>
                                                        </label>
                                                        <input type="file" id="image-input" name="building_pictures[]" accept="image/png, image/jpeg, image/jpg, image/gif" multiple hidden>
                                                        <div class="image-preview" id="image-preview">
                                                            <p>No images selected</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <h4>
                                                <button class="btn w-100 text-start" style="background-color: #D3D3D3;" type="button" data-bs-toggle="collapse" data-bs-target="#documents" aria-expanded="false" aria-controls="documents">
                                                    Documents <i class="fa fa-chevron-down"></i>
                                                </button>
                                            </h4>
                                            <div id="documents" class="collapse show collapsible-section">
                                                <button type="button" id="add-document" class="btn btn-light mt-21 mb-2">Add Document</button>
                                                <div id="documents-container" class="container">
                                                    <!-- Document fields will be appended here -->
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <button type="submit" class="btn btn-primary mt-2 w-100">Save</button>
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

    <!-- Organizations images show and remove script -->
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedImages = [];
            let currentIndex = 0;

            const imageInput = document.getElementById('imageInput');

            imageInput.addEventListener('change', handleImageSelection);

            function handleImageSelection(event) {
                const files = Array.from(event.target.files);
                selectedImages = [...selectedImages, ...files]; // Append new files to existing ones
                currentIndex = selectedImages.length - files.length; // Set the index to the start of the new images
                renderThumbnails();
                showImage();
                updateImageInput();
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
                updateImageInput();
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
    </script> -->

    <!-- Image script -->
    <script>
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');

        imageInput.addEventListener('change', () => {
            const files = imageInput.files;
            imagePreview.innerHTML = ''; // Clear any previous content

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
                            if (imagePreview.children.length === 0) {
                                imagePreview.innerHTML = '<p>No images selected</p>';
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

    <!-- Documents Script -->
    <script>
        let documentIndex = 0;
        const selectedDocumentTypes = new Set();
        const documentTypeMap = new Map();

        // Ensure DocumentsTypes is available globally or set it from Blade template
        const DocumentsTypes = @json($documentTypes);
        // const DocumentsTypes = {
        //     1: "Building Permit",
        //     2: "Occupancy Certificate",
        //     3: "Completion Certificate"
        // };

        // Create options for the document type select element
        const createDocumentOptions = () => {
            let documentOptions = `<option value="" disabled selected>Select Document Type</option>`;
            for (const [value_id, value_name] of Object.entries(DocumentsTypes)) {
                documentOptions += `<option value="${value_name}">${value_name}</option>`;
            }
            return documentOptions;
        };

        // Function to get the number of document types
        const getDocumentTypesCount = () => {
            return Object.keys(DocumentsTypes).length;
        };

        document.getElementById('add-document').addEventListener('click', function () {
            const container = document.getElementById('documents-container');
            const documentTypesCount = getDocumentTypesCount();

            if (documentIndex < documentTypesCount) {
                const div = document.createElement('div');
                div.classList.add('mb-3');
                div.dataset.index = documentIndex;
                div.innerHTML = `
                    <div class="row bg-light p-2">
                        <div class="col-md-2">
                            <label for="document-type-${documentIndex}" class="form-label">Document Type</label>
                            <select id="document-type-${documentIndex}" name="documents[${documentIndex}][type]" class="form-select document-type-select" required>
                                ${createDocumentOptions()}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="issue_date_${documentIndex}" class="form-label">Issue Date</label>
                            <input type="date" class="form-control" id="issue_date_${documentIndex}" name="documents[${documentIndex}][issue_date]"  >
                        </div>
                        <div class="col-md-2">
                            <label for="expiry_date_${documentIndex}" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date_${documentIndex}" name="documents[${documentIndex}][expiry_date]" >
                        </div>
                        <div class="col-md-4">
                            <label for="document-file-${documentIndex}" class="form-label">Document Files</label>
                            <input type="file" id="document-file-${documentIndex}" name="documents[${documentIndex}][files]" class="form-control" required>
                            <small>(max size: 5048mb)</small>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-sm remove-document mt-2" data-index="${documentIndex}">Remove</button>
                        </div>
                    </div>
                `;
                container.appendChild(div);

                documentIndex++;
            } else {
                alert('You cannot add more documents than the number of document types.');
            }
        });

        document.getElementById('documents-container').addEventListener('change', function (event) {
            if (event.target.classList.contains('document-type-select')) {
                const selectElement = event.target;
                const selectedType = selectElement.value;
                const index = selectElement.closest('div[data-index]').dataset.index;

                // Remove the previous document type from the set
                if (documentTypeMap.has(index)) {
                    selectedDocumentTypes.delete(documentTypeMap.get(index));
                }

                // Check if the new type is already selected
                if (selectedDocumentTypes.has(selectedType)) {
                    Swal.fire({
                        title: 'Already Selected',
                        text: 'This document type has already been selected.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    selectElement.value = "";
                } else {
                    selectedDocumentTypes.add(selectedType);
                    documentTypeMap.set(index, selectedType);
                }
            }

            if (event.target.type === 'file') {
                if (event.target.files.length > 5) {
                    alert('You can only upload a maximum of 5 files for each document type.');
                    event.target.value = "";
                }
            }
        });

        document.getElementById('documents-container').addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-document')) {
                const index = event.target.dataset.index;
                const documentDiv = document.querySelector(`div[data-index='${index}']`);
                const documentType = documentDiv.querySelector('.document-type-select').value;

                // Remove document type from selectedDocumentTypes
                selectedDocumentTypes.delete(documentType);
                documentTypeMap.delete(index);
                documentDiv.remove();
                documentIndex--;
            }
        });

        // Initialize document type options
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.document-type-select').forEach(select => {
                const existingOptions = select.querySelectorAll('option');
                if (existingOptions.length <= 1) { // Skip if options are already populated
                    Object.values(DocumentsTypes).forEach(type => {
                        const option = document.createElement('option');
                        option.value = type;
                        option.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                        select.appendChild(option);
                    });
                }
            });
        });
    </script>

@endpush
