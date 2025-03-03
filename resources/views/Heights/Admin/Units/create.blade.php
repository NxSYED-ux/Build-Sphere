@extends('layouts.app')

@section('title', 'Add Unit')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
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
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('units.index'), 'label' => 'Units'],
            ['url' => '', 'label' => 'Create Unit']
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
                                    <h4 class="mb-0">Add New Unit</h4>
                                    <a href="{{ route('units.index') }}" class="btn btn-secondary">Go Back</a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " >

                                        <form action="{{ route('units.store') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                                    <div class="row my-0 py-0">
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="unit_name">Unit Name</label>
                                                                <span class="required__field">*</span><br>
                                                                <input type="text" name="unit_name" id="unit_name" class="form-control @error('unit_name') is-invalid @enderror" value="{{ old('unit_name') }}" maxlength="50" placeholder="User Name" required>
                                                                @error('unit_name')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!--  -->
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <div class="form-group mb-3">
                                                                <label for="unit_type">Unit Type</label>
                                                                <span class="required__field">*</span><br>
                                                                <select name="unit_type" id="unit_type" class="form-select" required>
                                                                    <option value="" selected>Select Type</option>
                                                                    @foreach($unitTypes as $value)
                                                                        <option value="{{ $value->value_name }}" {{ old('unit_type') == $value->value_name ? 'selected' : '' }}>
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
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <div class="form-group mb-3">
                                                                <label for="sale_or_rent">Sale or Rent</label>
                                                                <span class="required__field">*</span><br>
                                                                <select name="sale_or_rent" id="sale_or_rent" class="form-select" required>
                                                                    <option value="" selected>Select Sale or Rent</option>
                                                                    <option value="sale" {{ old('sale_or_rent') == 'sale' ? 'selected' : '' }}>Sale</option>
                                                                    <option value="rent" {{ old('unit_type') == 'rent' ? 'selected' : '' }}>Rent</option>
                                                                    <option value="not available" {{ old('unit_type') == 'not available' ? 'selected' : '' }}>Not Available</option>
                                                                </select>
                                                                @error('sale_or_rent')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!--  -->
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <div class="form-group mb-3">
                                                                <label for="availability_status">Availability Status</label>
                                                                <span class="required__field">*</span><br>
                                                                <select name="availability_status" id="availability_status" class="form-select" required>
                                                                    <option value="" selected>Select Availability Status</option>
                                                                    <option value="available" {{ old('availability_status') == 'available' ? 'selected' : '' }}>Available</option>
                                                                    <option value="rented" {{ old('availability_status') == 'rented' ? 'selected' : '' }}>Rented</option>
                                                                    <option value="sold" {{ old('availability_status') == 'sold' ? 'selected' : '' }}>Sold</option>
                                                                    <option value="not available" {{ old('availability_status') == 'not available' ? 'selected' : '' }}>Not Available</option>
                                                                </select>
                                                                @error('availability_status')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!--  -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="price">Price</label>
                                                                <span class="required__field">*</span><br>
                                                                <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="Enter unit price" required>
                                                                @error('price')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!--  -->
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

                                                        <!--  -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="description">Description</label>
                                                                <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" maxlength="50" placeholder="Description">
                                                                @error('description')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!-- Oganization -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="organization_id">Organization</label>
                                                                <span class="required__field">*</span><br>
                                                                <select class="form-select" id="organization_id" name="organization_id" value="{{ old('organization_id') }}" required>
                                                                    <option value="" disabled {{ old('organization_id') === null ? 'selected' : '' }}>Select Organization</option>
                                                                    @foreach($organizations as $organization)
                                                                        <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
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

                                                        <!-- Building -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="building_id">Building</label>
                                                                <span class="required__field">*</span><br>
                                                                <select class="form-select" id="building_id" name="building_id" value="{{ old('building_id') }}" required>
                                                                    <option value="" disabled {{ old('building_id') === null ? 'selected' : '' }}>Select Building</option>
                                                                </select>
                                                                @error('building_id')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                       <!-- Level -->
                                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                                            <div class="form-group mb-3">
                                                                <label for="level_id">Level</label>
                                                                <span class="required__field">*</span><br>
                                                                <select class="form-select" id="level_id" name="level_id" value="{{ old('level_id') }}" required>
                                                                    <option value="" disabled {{ old('level_id') === null ? 'selected' : '' }}>Select Level</option>
                                                                </select>
                                                                @error('level_id')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                                    <div class="image-input-container mt-4">
                                                        <label for="image-input" class="custom-file-label">
                                                            <span>Choose Images</span>
                                                        </label>
                                                        <input type="file" id="image-input" name="unit_pictures[]" accept="image/png, image/jpeg, image/jpg, image/gif" multiple hidden>
                                                        <div class="image-preview" id="image-preview">
                                                            <p>No images selected</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="status" value="Approved">
                                            <button type="submit" class="btn btn-primary">Save</button>
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

    <script>
        $(document).ready(function() {
            // Fetch buildings based on selected organization
            $('#organization_id').change(function() {
                var organizationId = $(this).val();

                if (organizationId) {
                    $.ajax({
                        url: '{{ route('organizations.buildings', ':id') }}'.replace(':id', organizationId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            var buildingSelect = $('#building_id');
                            buildingSelect.empty();
                            buildingSelect.append('<option value="" disabled selected>Select Building</option>');

                            $.each(data.buildings, function(key, value) {
                                buildingSelect.append('<option value="' + key + '">' + value + '</option>');
                            });

                            $('#level_id').empty().append('<option value="" disabled selected>Select Level</option>');
                        },
                        error: function(xhr) {
                            console.error('Error fetching buildings:', xhr.responseText);
                        }
                    });
                } else {
                    $('#building_id').empty().append('<option value="" disabled selected>Select Building</option>');
                    $('#level_id').empty().append('<option value="" disabled selected>Select Level</option>');
                }
            });

            // Fetch levels based on selected building
            $('#building_id').change(function() {
                var buildingId = $(this).val();

                if (buildingId) {
                    $.ajax({
                        url: '{{ route('buildings.levels', ':id') }}'.replace(':id', buildingId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            var levelSelect = $('#level_id');
                            levelSelect.empty();
                            levelSelect.append('<option value="" disabled selected>Select Level</option>');

                            $.each(data.levels, function(key, value) {
                                levelSelect.append('<option value="' + key + '">' + value + '</option>');
                            });
                        },
                        error: function(xhr) {
                            console.error('Error fetching levels:', xhr.responseText);
                        }
                    });
                } else {
                    $('#level_id').empty().append('<option value="" disabled selected>Select Level</option>');
                }
            });
        });
    </script>


@endpush
