@extends('layouts.app')

@section('title', 'Edit Membership')

@push('styles')
    <style>
        .edit-membership-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .form-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .form-title {
            font-weight: 700;
            color: #2c3e50;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #3498db;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f1f1;
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
        .current-image-container {
            margin-bottom: 15px;
        }
        .current-image-label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Memberships'],
            ['url' => '', 'label' => 'Edit Membership']
        ]"
    />

    <x-Owner.side-navbar :openSections="['Memberships']"/>
    <x-error-success-model />

    <div id="main">
        <section class="content mt-1 mb-5 mx-2">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="edit-membership-container">
                            <div class="form-header">
                                <h3 class="form-title">Edit Membership</h3>
                                <p class="text-muted mb-0">Update the details of this membership plan</p>
                            </div>

                            <form action="#" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Basic Information Section -->
                                <div class="form-section">
                                    <h5 class="section-title">Basic Information</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Membership Name</label>
                                                <input type="text" class="form-control" id="name" name="name" required
                                                       value="{{ old('name', $membership->name) }}" placeholder="e.g. Premium Gym Access">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category</label>
                                                <select class="form-select" id="category" name="category" required>
                                                    <option value="">Select Category</option>
                                                    <option value="gym" {{ old('category', $membership->category) == 'gym' ? 'selected' : '' }}>Gym/Fitness</option>
                                                    <option value="restaurant" {{ old('category', $membership->category) == 'restaurant' ? 'selected' : '' }}>Restaurant/Dining</option>
                                                    <option value="spa" {{ old('category', $membership->category) == 'spa' ? 'selected' : '' }}>Spa/Wellness</option>
                                                    <option value="retail" {{ old('category', $membership->category) == 'retail' ? 'selected' : '' }}>Retail/Shopping</option>
                                                    <option value="other" {{ old('category', $membership->category) == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required
                                                  placeholder="Brief description of the membership benefits">{{ old('description', $membership->description) }}</textarea>
                                    </div>
                                </div>

                                <!-- Pricing Section -->
                                <div class="form-section">
                                    <h5 class="section-title">Pricing Details</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Monthly Price ($)</label>
                                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required
                                                       value="{{ old('price', $membership->price) }}" placeholder="49.99">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="original_price" class="form-label">Original Price ($)</label>
                                                <input type="number" class="form-control" id="original_price" name="original_price" min="0" step="0.01"
                                                       value="{{ old('original_price', $membership->original_price) }}" placeholder="59.99">
                                                <small class="text-muted">Leave blank if no discount</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_cycle" class="form-label">Billing Cycle</label>
                                                <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                                    <option value="monthly" {{ old('billing_cycle', $membership->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                    <option value="quarterly" {{ old('billing_cycle', $membership->billing_cycle) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                    <option value="yearly" {{ old('billing_cycle', $membership->billing_cycle) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                                </select>
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
                                                @if($membership->image)
                                                    <div class="current-image-container">
                                                        <span class="current-image-label">Current Image:</span>
                                                        <img src="{{ asset('storage/' . $membership->image) }}" class="preview-image" style="max-width: 200px;">
                                                    </div>
                                                @endif
                                                <label class="form-label">Update Image</label>
                                                <div class="form-image-upload" onclick="document.getElementById('image_upload').click()">
                                                    <div class="text-center">
                                                        <x-icon name="image" size="30" class="text-muted mb-2" />
                                                        <p class="mb-1">Click to upload new image</p>
                                                        <small class="text-muted">Recommended size: 800x600px</small>
                                                        <input type="file" id="image_upload" name="image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                                                    </div>
                                                    <img id="image_preview" class="preview-image d-none">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Badge Options</label>
                                                <div class="card p-3">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="show_discount_badge" name="show_discount_badge"
                                                            {{ old('show_discount_badge', $membership->show_discount_badge) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="show_discount_badge">
                                                            Show Discount Badge
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="show_popular_badge" name="show_popular_badge"
                                                            {{ old('show_popular_badge', $membership->show_popular_badge) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="show_popular_badge">
                                                            Mark as "Popular"
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Options -->
                                <div class="form-section">
                                    <h5 class="section-title">Additional Options</h5>
                                    <div class="mb-3">
                                        <label for="features" class="form-label">Key Features</label>
                                        <div id="features-container">
                                            @foreach(old('features', $membership->features ?? ['']) as $index => $feature)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" name="features[]"
                                                           value="{{ $feature }}" placeholder="Feature {{ $index + 1 }}">
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeFeature(this)">
                                                        <x-icon name="close" size="16" />
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addFeature()">
                                            <x-icon name="add" size="16" class="me-1" />
                                            Add Feature
                                        </button>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex justify-content-between pt-3">
                                    <a href="#" class="btn btn-outline-secondary">
                                        <x-icon name="arrow-back" size="16" class="me-1" />
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <x-icon name="save" size="16" class="me-1" />
                                        Update Membership
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

        // Dynamic feature fields
        function addFeature() {
            const container = document.getElementById('features-container');
            const count = container.children.length + 1;
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="text" class="form-control" name="features[]" placeholder="Feature ${count}">
                <button type="button" class="btn btn-outline-danger" onclick="removeFeature(this)">
                    <x-icon name="close" size="16" />
                </button>
            `;
            container.appendChild(div);
        }

        function removeFeature(button) {
            if (document.getElementById('features-container').children.length > 1) {
                button.parentElement.remove();
            }
        }

        // Initialize the form with existing features
        document.addEventListener('DOMContentLoaded', function() {
            // If no features exist, add one empty field
            if (document.getElementById('features-container').children.length === 0) {
                addFeature();
            }
        });
    </script>
@endpush
