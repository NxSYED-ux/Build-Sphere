@extends('layouts.app')

@section('title', 'Create Membership')

@push('styles')
    <style>
        body {
            background-color: #f8f9fa;
        }
        #main {
            margin-top: 50px;
        }
        .create-membership-container {
            margin: 0 auto;
            padding: 30px;
            background: var(--body-card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .form-header {
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .form-title {
            font-weight: 700;
            color: var(--sidenavbar-text-color);
        }
        .form-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #3498db;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        .form-image-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .form-image-upload:hover {
            border-color: #3498db;
            background: #f8fafc;
        }
        .preview-image {
            max-width: 200px;
            max-height: 150px;
            margin-top: 15px;
            border-radius: 6px;
        }
        .discount-badge-preview {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #e74c3c;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Memberships'],
            ['url' => '', 'label' => 'Create Membership']
        ]"
    />

    <x-Owner.side-navbar :openSections="['Memberships']"/>
    <x-error-success-model />

    <div id="main">
        <section class="content mt-1 mb-5 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="create-membership-container">
                            <div class="form-header">
                                <h3 class="form-title">Create New Membership</h3>
                            </div>

                            <form action="{{ route('owner.memberships.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- Basic Information Section -->
                                <div class="form-section">
                                    <h5 class="section-title">Basic Information</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Membership Name <span class="required__field">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name" required
                                                       value="{{ old('name') }}" placeholder="e.g. Premium Gym Access">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category <span class="required__field">*</span></label>
                                                <select class="form-select" id="category" name="category" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($types as $type)
                                                        <option value="{{ $type }}" {{ old('category') == $type ? 'selected' : '' }}>
                                                            {{ $type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Buildings <span class="required__field">*</span></label>
                                                <select class="form-select" id="building_id" name="building_id" required>
                                                    <option value="" {{ old('building_id') === null ? 'selected' : '' }}>Select Building</option>
                                                    @foreach($buildings as $building)
                                                        <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                                            {{ $building->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Units <span class="required__field">*</span></label>
                                                <select class="form-select" id="unit_id" name="unit_id" required>
                                                    <option value="">Select Unit</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="scans_per_day" class="form-label">Scans Per Day <span class="required__field">*</span></label>
                                                <input type="number" class="form-control" id="scans_per_day" name="scans_per_day"
                                                       min="1" required value="{{ old('scans_per_day') }}" placeholder="ie.100">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status <span class="required__field">*</span></label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="">Select Status</option>
                                                    @foreach($statuses as $status)
                                                        <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                                            {{ $status }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="url" class="form-label">Membership Url <span class="required__field">*</span></label>
                                                <input type="text" class="form-control" id="url" name="url" required
                                                       value="{{ old('url') }}" placeholder="e.g. membership url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"
                                                  placeholder="Brief description of the membership benefits">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <!-- Pricing Section -->
                                <div class="form-section">
                                    <h5 class="section-title">Pricing Details</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="currency" class="form-label">Currency <span class="required__field">*</span></label>
                                                <select class="form-select" id="currency" name="currency" required>
                                                    @foreach($currency as $curr)
                                                        <option value="{{ $curr }}" {{ old('currency') == $curr ? 'selected' : '' }}>
                                                            {{ $curr }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Monthly Price <span class="required__field">*</span></label>
                                                <input type="number" class="form-control" id="price" name="price"
                                                       min="0" step="0.01" required value="{{ old('price') }}" placeholder="49.99">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="original_price" class="form-label">Original Price</label>
                                                <input type="number" class="form-control" id="original_price" name="original_price"
                                                       min="0" step="0.01" value="{{ old('original_price') }}" placeholder="59.99">
                                                <small class="text-muted">Leave blank if no discount</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_cycle" class="form-label">Billing Cycle <span class="required__field">*</span></label>
                                                <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                                    <option value="monthly" {{ old('billing_cycle', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                    <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="months_field_container">
                                            <div class="mb-3">
                                                <label for="number_of_months" class="form-label">Number of Months <span class="required__field">*</span></label>
                                                <input type="number" class="form-control" id="number_of_months" name="duration_months"
                                                       min="1" value="{{ old('number_of_months', 1) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="years_field_container" style="display: none;">
                                            <div class="mb-3">
                                                <label for="number_of_years" class="form-label">Number of Years</label>
                                                <input type="number" class="form-control" id="number_of_years" name="duration_months"
                                                       min="1" value="{{ old('number_of_years', 1) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Visual Presentation -->
                                <div class="form-section">
                                    <h5 class="section-title">Visual Presentation</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Membership Image<span class="required__field">*</span></label>
                                                <div class="form-image-upload" onclick="document.getElementById('image_upload').click()">
                                                    <div class="text-center">
                                                        <x-icon name="image" size="30" class="text-muted mb-2" />
                                                        <p class="mb-1">Click to upload image</p>
                                                        <small class="text-muted">Recommended size: 800x600px</small>
                                                        <input type="file" id="image_upload" name="image" accept="image/*" style="display: none;" onchange="previewImage(this)" required>
                                                    </div>
                                                    <img id="image_preview" class="preview-image d-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="pt-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <x-icon name="save" size="16" class="me-1" />
                                        Create Membership
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('scripts')
    <script>
        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('image_preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const billingCycleSelect = document.getElementById('billing_cycle');
            const monthsFieldContainer = document.getElementById('months_field_container');
            const yearsFieldContainer = document.getElementById('years_field_container');

            // Set initial state based on old input or default
            if (billingCycleSelect.value === 'yearly') {
                monthsFieldContainer.style.display = 'none';
                yearsFieldContainer.style.display = 'block';
            } else {
                monthsFieldContainer.style.display = 'block';
                yearsFieldContainer.style.display = 'none';
            }

            billingCycleSelect.addEventListener('change', function() {
                if (this.value === 'yearly') {
                    monthsFieldContainer.style.display = 'none';
                    yearsFieldContainer.style.display = 'block';
                } else {
                    monthsFieldContainer.style.display = 'block';
                    yearsFieldContainer.style.display = 'none';
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // When either category or building selection changes
            $('#category, #building_id').change(function() {
                var category = $('#category').val();
                var buildingId = $('#building_id').val();

                // Clear units dropdown if either field is empty
                if (!category || !buildingId) {
                    $('#unit_id').html('<option value="">Select Unit</option>');
                    return;
                }

                // Make AJAX call
                $.ajax({
                    url: "{{ route('owner.buildings.units.byType', ['building' => ':building', 'type' => ':type']) }}"
                        .replace(':building', buildingId)
                        .replace(':type', category),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var unitsDropdown = $('#unit_id');
                        unitsDropdown.empty();
                        unitsDropdown.append('<option value="">Select Unit</option>');

                        if (response.units.length > 0) {
                            $.each(response.units, function(key, unit) {
                                var selected = unit.id == "{{ old('unit_id') }}" ? 'selected' : '';
                                unitsDropdown.append('<option value="' + unit.id + '" ' + selected + '>' + unit.unit_name + '</option>');
                            });
                        } else {
                            unitsDropdown.append('<option value="">No units available</option>');
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            // Trigger change event if old values exist
            @if(old('building_id') && old('category'))
            $('#building_id, #category').trigger('change');
            @endif
        });
    </script>
@endpush
