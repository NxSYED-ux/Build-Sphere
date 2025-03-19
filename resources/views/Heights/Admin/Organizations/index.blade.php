@extends('layouts.app')

@section('title', 'Organizations')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .nav-tabs .nav-link {
            background-color: var(--nav-tabs-inactive-bg-color) !important; /* Change to your desired color */
            color: var(--nav-tabs-inactive-text-color) !important;
            border-bottom: 1px solid var(--nav-tabs-inactive-border-color) !important; /* Corrected */
        }
        .nav-tabs .nav-link.active {
            background-color: var(--nav-tabs-active-bg-color) !important; /* Change to your desired color */
            color: var(--nav-tabs-active-text-color) !important;
        }

        /* DataTables Entries Dropdown */
        .dataTables_wrapper .dataTables_length select {
            background-color: var(--dataTable-paginate-menu-bg-color);
            color: var(--dataTable-paginate-menu-text-color);
            border: 1px solid var(--dataTable-paginate-menu-border-color);
        }
        .dataTables_wrapper .dataTables_length label {
            color: var(--dataTable-paginate-menu-label-color);
        }
        /* DataTables Search Box */
        .dataTables_wrapper .dataTables_filter input {
            background-color: var(--dataTable-search-input-bg-color);
            color: var(--dataTable-search-input-text-color);
            border: 1px solid var(--dataTable-search-input-border-color);
        }
        .dataTables_wrapper .dataTables_filter label {
            color: var(--dataTable-search-label-color);
        }
        .dataTables_filter input::placeholder {
            color: var(--dataTable-search-placeholder-color); /* Standard */
        }
        .dataTables_filter input::-webkit-input-placeholder {
            color: var(--dataTable-search-placeholder-color); /* WebKit browsers */
        }
        .dataTables_filter input::-moz-placeholder {
            color: var(--dataTable-search-placeholder-color); /* Mozilla Firefox 19+ */
        }
        .dataTables_filter input:-ms-input-placeholder {
            color: var(--dataTable-search-placeholder-color); /* Internet Explorer 10+ */
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

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Organizations']
        ]"
    />
    <!--  -->
    <x-Admin.side-navbar :openSections="['Organizations']" />
    <x-error-success-model />

    @php
        $activeTab = old('activeTab', 'Tab1');
    @endphp

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $activeTab === 'Tab1' ? 'active' : '' }}" id="dropdwon-types-tab" data-bs-toggle="tab" href="#dropdwon-types" role="tab" aria-controls="dropdwon-types" aria-selected="{{ $activeTab === 'Tab1' ? 'true' : 'false' }}">Organization</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $activeTab === 'Tab2' ? 'active' : '' }}" id="dropdwon-values-tab" data-bs-toggle="tab" href="#dropdwon-values" role="tab" aria-controls="dropdwon-values" aria-selected="{{ $activeTab === 'Tab2' ? 'true' : 'false' }}">Add Organization</a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-0" id="myTabContent">
                                    <!-- Organization Tab -->
                                    <div class="tab-pane fade {{ $activeTab === 'Tab1' ? 'show active' : '' }}" id="dropdwon-types" role="tabpanel" aria-labelledby="dropdwon-types-tab">
                                        <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                            <div class="card-body" style="overflow-x: auto;">
{{--                                                <h4 class="mb-4">Organizations</h4>--}}
                                                <div style="overflow-x: auto;">
                                                    <table id="organizationTable" class="table shadow-sm table-hover table-striped">
                                                        <thead class="shadow">
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Picture</th>
                                                                <th>Name</th>
                                                                <th>Owner</th>
                                                                <th>City</th>
                                                                <th>Membership Start Date</th>
                                                                <th>Membership End Date</th>
                                                                <th>Status</th>
                                                                <th class="text-center" style="width: 70px;">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($organizations ?? [] as $organization)
                                                                <tr>
                                                                    <td>{{ $organization->id }}</td>
                                                                    <td>
                                                                        <img src="{{ $organization->pictures->isNotEmpty() ? asset($organization->pictures->first()->file_path) : asset('https://via.placeholder.com/150') }}" alt="Organization Picture" class="rounded-circle" width="50" height="50">
                                                                    </td>
                                                                    <td>{{ $organization->name }}</td>
                                                                    <td>{{ $organization->owner->name }}</td>
                                                                    <td>{{ $organization->address->city ?? 'N/A' }}</td>
                                                                    <td>{{ $organization->membership_start_date ? $organization->membership_start_date->format('Y-m-d') : '' }}</td>
                                                                    <td>{{ $organization->membership_end_date ? $organization->membership_end_date->format('Y-m-d') : '' }}</td>
                                                                    <td>{{ $organization->status }}</td>
                                                                    <td class="text-center" style="width: 70px;">
                                                                        <a href="{{ route('organizations.edit', $organization->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"> <i class="fa fa-pencil mx-2" style="font-size: 20px;"></i> </a>

                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="9" class="text-center">No organizations found.</td>
                                                                    </tr>
                                                                @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Add Organization Tab -->
                                    <div class="tab-pane fade {{ $activeTab === 'Tab2' ? 'show active' : '' }}" id="dropdwon-values" role="tabpanel" aria-labelledby="dropdwon-values-tab">
                                        <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                            <div class="card-body" style="overflow-x: auto;">
{{--                                                <h4 class="mb-4">Add Organization</h4>--}}
                                                    <form action="{{ route('organizations.store') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf

                                                        <div class="row">
                                                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">

                                                                <div class="row my-0 py-0">
                                                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                                                        <div class="form-group mb-3">
                                                                            <label for="name">Name</label>
                                                                            <span class="required__field">*</span><br>
                                                                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" maxlength="50" placeholder="Organization Name" required>
                                                                            @error('name')
                                                                                <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <!-- Organization -->
                                                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                                                        <div class="form-group mb-3">
                                                                            <label for="owner_id">Owner</label>
                                                                            <span class="required__field">*</span><br>
                                                                            <select class="form-select" id="owner_id" name="owner_id" required>
                                                                                <option value="" disabled {{ old('owner_id') === null ? 'selected' : '' }}>Select Organization</option>
                                                                                @foreach($owners as $id => $name)
                                                                                    <option value="{{ $id }}" {{ old('owner_id') == $id ? 'selected' : '' }}>
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


                                                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                                                        <div class="form-group mb-3">
                                                                            <label for="membership_start_date" >Membership Start Date</label>
                                                                            <span class="required__field">*</span><br>
                                                                            <input type="date" class="form-control" id="membership_start_date" name="membership_start_date" value="{{ old('membership_start_date', date('Y-m-d')) }}" required>
                                                                            @error('membership_start_date')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                                                        <div class="form-group mb-3">
                                                                            <label for="membership_end_date" >Membership End Date</label>
                                                                            <span class="required__field">*</span><br>
                                                                            <input type="date" class="form-control" id="membership_end_date" name="membership_end_date" value="{{ old('membership_end_date') }}" required>
                                                                            @error('membership_end_date')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <!--  -->
                                                                    <div class="col-sm-12 col-md-6 col-lg-6">
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
                                                                    <div class="col-sm-12 col-md-6 col-lg-6">
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
                                                                    <div class="col-sm-12 col-md-6 col-lg-6">
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
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                                                                <div class="image-input-container mt-4">
                                                                    <label for="image-input" class="custom-file-label">
                                                                        <span>Choose Images</span>
                                                                    </label>
                                                                    <input type="file" id="image-input" name="organization_pictures[]" accept="image/png, image/jpeg, image/jpg, image/gif" multiple hidden>
                                                                    <div class="image-preview" id="image-preview">
                                                                        <p>No images selected</p>
                                                                    </div>
                                                                    @error('organizations_images')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary">Create Organizations</button>
                                                    </form>
                                            </div>
                                        </div>
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

    <!-- Add DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <!-- Add DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <!-- Tab Active -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activeTab = "{{ $activeTab }}";
            if (activeTab === 'Tab2') {
                const tabTrigger = new bootstrap.Tab(document.querySelector('#tab_2'));
                tabTrigger.show();
            }
        });
    </script>
    <!-- DataTables script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            new DataTable("#organizationTable", {
                pageLength: 10,
                lengthMenu: [10, 20, 50, 100],
                language: {
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    searchPlaceholder: "Search users..."
                }
            });
        });
    </script>

    <!-- Dates valdations  -->
    <script>
        document.getElementById('membership_start_date').addEventListener('change', validateDates);
        document.getElementById('membership_end_date').addEventListener('change', validateDates);

        function validateDates() {
            const membership_start_date = new Date(document.getElementById('membership_start_date').value);
            const membership_end_date = new Date(document.getElementById('membership_end_date').value);

            if (membership_start_date && membership_end_date && membership_end_date <= membership_start_date) {
                document.getElementById('membership_end_date').value = '';
                Swal.fire({
                    title: 'Invalid End Date',
                    text: 'Membership End Date must be after or equal the Start Date.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
    </script>

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

@endpush
