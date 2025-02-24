@extends('layouts.app')

@section('title', 'Organizations')

@push('styles')
    <style>
        body { 
        }
        #main { 
            margin-top: 45px;
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

        .image-thumbnail {
            display: inline-block;
            margin: 5px;
            position: relative;
        }

        .thumbnail-remove {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255, 0, 0, 0.5);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
@endpush 

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false"/>
    <!--  -->
    <x-Admin.side-navbar :openSections="['Organizations']" /> 
    <x-error-success-model />
 
    @php
        $activeTab = old('activeTab', 'Tab1');
    @endphp

    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{ url('admin_dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Organizations</a></li>
                </ol>
            </nav>
        </section>

        <section class="content content-top  my-3 mx-2">
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
                                                <h3 class="mb-4">Organizations</h3>
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
                                                            @foreach ($organizations as $organization)
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
                                                            @endforeach
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
                                                <h3 class="mb-4">Add Organization</h3> 
                                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                                    <div class="card-body " > 

                                                        <form action="{{ route('organizations.store') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="row my-0 py-0">
                                                                <div class="col-sm-12 col-md-6 col-lg-4">
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


                                                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                                                    <label for="membership_start_date" >Membership Start Date</label>
                                                                    <span class="required__field">*</span><br>
                                                                    <input type="date" class="form-control" id="membership_start_date" name="membership_start_date" value="{{ old('membership_start_date', date('Y-m-d')) }}" required>
                                                                    @error('membership_start_date')
                                                                        <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                                                    <label for="membership_end_date" >Membership End Date</label>
                                                                    <span class="required__field">*</span><br>
                                                                    <input type="date" class="form-control" id="membership_end_date" name="membership_end_date" value="{{ old('membership_end_date') }}" required>
                                                                    @error('membership_end_date')
                                                                        <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div> 

                                                                <!--  -->
                                                                <div class="col-lg-4 col-md-6 col-sm-12">
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
                                                                <div class="col-lg-4 col-md-6 col-sm-12">
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
                                                                <div class="col-lg-4 col-md-6 col-sm-12">
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
                                                                <div class="col-sm-12 col-md-6 col-lg-4">
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
                                                                <div class="col-sm-12 col-md-6 col-lg-4">
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
                                                            <div class="row">                                                           
                                                                <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                                                    <label for="imageInput" class="form-label">Organization Images </label>
                                                                    <input type="file" id="imageInput" name="organization_images[]" class="form-control" accept="image/png, image/jpeg, image/jpg, image/gif" multiple>
                                                                    <small>(max size: 2048mb | 520x520 pixels)</small>
                                                                    @error('organizations_images')
                                                                        <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-lg-8 col-md-8 col-sm-12 mb-3">
                                                                    <div id="imageThumbnails" class="mt-3">
                                                                        <!-- Image thumbnails will be inserted here -->
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
        $(document).ready(function() {
            $('#organizationTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 20, 50, 100],
                "language": {
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            }); 
        }); 
    </script>  

    <!-- Tool tip script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
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
    
@endpush
