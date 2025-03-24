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
        color: #fff !important;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
        position: relative;
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
        align-items: center;
        border: 2px dashed var(--sidenavbar-text-color);
        border-radius: 10px;
        padding: 15px;
        height: 275px;
        background-color: var(--main-background-color);
        margin-top: 10px;
        overflow-y: auto;
        text-align: center;
    }

    .image-input-container .image-preview::-webkit-scrollbar {
        width: 5px;
        position: absolute;
        right: -2px;
    }

    .image-input-container .image-preview::-webkit-scrollbar-thumb {
        background-color: var(--sidenavbar-text-color);
        border-radius: 10px;
    }

    .image-input-container .image-preview::-webkit-scrollbar-track {
        background: transparent;
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

    @media (max-width: 575.98px) {
        .image-input-container .image-item {
            width: 100px;
            height: 100px;
        }
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
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.units.index'), 'label' => 'Units'],
            ['url' => '', 'label' => 'Create Unit']
        ]"
/>

<x-Owner.side-navbar :openSections="['Buildings', 'Units']" />
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
                                <a href="{{ route('owner.units.index') }}" class="btn btn-secondary">Go Back</a>
                            </div>
                            <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                <div class="card-body " >

                                    <form action="{{ route('owner.units.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                                <div class="row my-0 py-0">
                                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                                        <div class="form-group mb-3">
                                                            <label for="unit_name">Unit Name</label>
                                                            <span class="required__field">*</span><br>
                                                            <input type="text" name="unit_name" id="unit_name" class="form-control @error('unit_name') is-invalid @enderror" value="{{ old('unit_name') }}" maxlength="50" placeholder="Unit Name" required>
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

                                                    <!--   -->
                                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                                        <div class="form-group mb-3">
                                                            <label for="building_id">Building</label>
                                                            <span class="required__field">*</span><br>
                                                            <select class="form-select" id="building_id" name="building_id"  required>
                                                                <option value="" disabled {{ old('building_id') === null ? 'selected' : '' }}>Select Building</option>
                                                                @foreach($buildings as $building)
                                                                    <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                                                        {{ $building->name }}
                                                                    </option>
                                                                @endforeach
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
                                                <div class="row  d-none d-md-block">
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary w-100">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                                <div class="image-input-container mt-1">
                                                    <label for="image-input" class="-sm-block d-md-none btn btn-primary d-flex align-items-center justify-content-center w-100" style="margin-top: 13px !important; color: #ffff !important;">
                                                        <i class='bx bx-upload fs-5 px-1'></i> Choose Images
                                                    </label>
                                                    <input type="file" id="image-input" name="unit_pictures[]" accept="image/png, image/jpeg, image/jpg, image/gif" multiple hidden>
                                                    <div class="image-preview" id="image-preview">
                                                        <p id="image-message">No images selected</p>
{{--                                                        <p id="error-message" class="text-danger mt-2"></p>--}}
                                                    </div>
                                                    <label for="image-input" class="d-none d-md-block btn btn-primary d-flex align-items-center justify-content-center w-100" style="margin-top: 13px !important; color: #ffff !important;">
                                                        <i class='bx bx-upload fs-5 px-1'></i> Choose Images
                                                    </label>

                                                </div>
                                            </div>

                                        </div>
                                        <input type="hidden" name="status" value="Approved">
                                        <div class="col-12 d-sm-block d-md-none mt-3">
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
<!-- Image script -->
<script>
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');
    const imageMessage = document.getElementById('image-message');

    imageInput.addEventListener('change', () => {
        const files = imageInput.files;

        if (files.length > 4) {
            imageMessage.textContent = 'You can only select up to 4 images.';
            imageMessage.style.color = 'red';
            imageInput.value = '';
            return;
        } else {
        }

        imagePreview.innerHTML = '';

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
    document.addEventListener("DOMContentLoaded", function () {
        // const organizationSelect = document.getElementById("organization_id");
        const buildingSelect = document.getElementById("building_id");
        const levelSelect = document.getElementById("level_id");

        // Fetch levels based on selected building
        buildingSelect.addEventListener("change", function () {
            const buildingId = this.value;

            if (buildingId) {
                fetch(`{{ route('buildings.levels', ':id') }}`.replace(':id', buildingId), {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Accept": "application/json"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Clear previous options
                        levelSelect.innerHTML = `<option value="" disabled selected>Select Level</option>`;

                        // Populate new options
                        for (const [key, value] of Object.entries(data.levels)) {
                            const option = document.createElement("option");
                            option.value = key;
                            option.textContent = value;
                            levelSelect.appendChild(option);
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching levels:", error);
                    });
            } else {
                // Reset level dropdown if no building is selected
                levelSelect.innerHTML = `<option value="" disabled selected>Select Level</option>`;
            }
        });
    });
</script>


@endpush
