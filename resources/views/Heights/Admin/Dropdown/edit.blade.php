@extends('layouts.app')

@section('title', 'Edit Value')

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
    </style>
@endpush

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false"/>
    <!--  -->
    <x-Admin.side-navbar :openSections="['AdminControl', 'Dropdown']" />
    <x-error-success-model />

    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{url('admin_dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('values.index') }}">Dropdown</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Edit Values</a></li>
                </ol>
            </nav>
        </section>

        <section class="content content-top my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">Edit Drop Down Values</h4>
                                    <a href="{{ route('values.index') }}" class="btn btn-secondary">Go Back</a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <form action="{{ route('values.update', $value->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="value_name" class="form-label">Value Name</label>
                                                    <span class="required__field">*</span><br>
                                                    <input type="text" class="form-control" id="value_name" name="value_name" value="{{ old('value_name', $value->value_name) }}" maxlength="50" placeholder="Value Name" required>
                                                    @error('value_name')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <input class="form-control" id="description" name="description" value="{{ old('description', $value->description) }}" maxlength="250" placeholder="Description">
                                                    @error('description')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="dropdown_type_id" class="form-label">Dropdown Type</label>
                                                    <span class="required__field">*</span><br>
                                                    <select class="form-select" id="dropdown_type_id" name="dropdwon_type_id" required>
                                                        <option value="">Select Type</option>
                                                        @foreach ($types as $type)
                                                            <option value="{{ $type->id }}" data-parent-type-id="{{ $type->parent_type_id }}" {{ old('dropdown_type_id', $value->dropdown_type_id) == $type->id ? 'selected' : '' }}>
                                                                {{ $type->type_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('dropdown_type_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="parent_value_id" class="form-label">
                                                        Parent Value
                                                    </label>
                                                    <select class="form-select" id="parent_value_id" name="parent_value_id">
                                                        <option value="">Select Parent Value</option>
                                                    </select>
                                                    @error('parent_value_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <span class="required__field">*</span><br>
                                                    <select class="form-select" id="status" name="status" required>
                                                        <option value="1" {{ old('status', $value->status) == 1 ? 'selected' : '' }}>Active</option>
                                                        <option value="0" {{ old('status', $value->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                                                </div>
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

    <!-- Control logic for link type and value ids -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const types = @json($types); // Pass types and their values to JavaScript
            const oldParentValueId = '{{ old("parent_value_id", $value->parent_value_id) }}'; // Use the old value or database value

            const dropdownTypeId = document.getElementById('dropdown_type_id');
            const parentValueId = document.getElementById('parent_value_id');
            const parentValueLabel = document.querySelector('label[for="parent_value_id"]');

            // Function to get values for a specific type ID
            function getValuesForType(typeId) {
                const type = types.find(t => t.id == typeId);
                return type ? type.values : [];
            }

            // Function to update parent values and required field state
            function updateParentValues() {
                const selectedTypeId = dropdownTypeId.value;
                const selectedTypeOption = dropdownTypeId.options[dropdownTypeId.selectedIndex];
                const parentTypeId = selectedTypeOption ? selectedTypeOption.getAttribute('data-parent-type-id') : null;

                // Clear existing options
                parentValueId.innerHTML = '<option value="">Select Parent Value</option>';
                parentValueId.required = false; // Default to not required
                parentValueLabel.querySelector('.required__field')?.remove(); // Remove the required indicator

                if (parentTypeId) {
                    const parentValues = getValuesForType(parentTypeId); // Get values for parent type ID

                    if (parentValues.length > 0) {
                        // Add options for parent values
                        parentValues.forEach(value => {
                            const option = document.createElement('option');
                            option.value = value.id;
                            option.textContent = value.value_name;

                            // Select the old value if it matches
                            if (value.id == oldParentValueId) {
                                option.selected = true;
                            }

                            parentValueId.appendChild(option);
                        });

                        // Mark the field as required
                        parentValueId.required = true;
                        const requiredIndicator = document.createElement('span');
                        requiredIndicator.classList.add('required__field');
                        requiredIndicator.textContent = '*';
                        parentValueLabel.appendChild(requiredIndicator);
                    }
                }
            }

            // Update parent values when the dropdown type changes
            dropdownTypeId.addEventListener('change', updateParentValues);

            // Initialize parent values on page load
            updateParentValues();
        });
    </script>

@endpush
