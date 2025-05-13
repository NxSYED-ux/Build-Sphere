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

                            <form action="#" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- Basic Information Section -->
                                <div class="form-section">
                                    <h5 class="section-title">Basic Information</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Membership Name</label>
                                                <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Premium Gym Access">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category</label>
                                                <select class="form-select" id="category" name="category" required>
                                                    <option value="">Select Category</option>
                                                    <option value="gym">Gym/Fitness</option>
                                                    <option value="restaurant">Restaurant/Dining</option>
                                                    <option value="spa">Spa/Wellness</option>
                                                    <option value="retail">Retail/Shopping</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Buildings</label>
                                                <select class="form-select" id="building_id" name="building_id" required>
                                                    <option value="">Select Building</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Units</label>
                                                <select class="form-select" id="unit_id" name="unit_id" required>
                                                    <option value="">Select Unit</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Brief description of the membership benefits"></textarea>
                                    </div>
                                </div>

                                <!-- Pricing Section -->
                                <div class="form-section">
                                    <h5 class="section-title">Pricing Details</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Monthly Price ($)</label>
                                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required placeholder="49.99">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="original_price" class="form-label">Original Price ($)</label>
                                                <input type="number" class="form-control" id="original_price" name="original_price" min="0" step="0.01" placeholder="59.99">
                                                <small class="text-muted">Leave blank if no discount</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_cycle" class="form-label">Billing Cycle</label>
                                                <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                                    <option value="monthly">Monthly</option>
                                                    <option value="quarterly">Quarterly</option>
                                                    <option value="yearly">Yearly</option>
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
                                                <label class="form-label">Membership Image</label>
                                                <div class="form-image-upload" onclick="document.getElementById('image_upload').click()">
                                                    <div class="text-center">
                                                        <x-icon name="image" size="30" class="text-muted mb-2" />
                                                        <p class="mb-1">Click to upload image</p>
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
                                                        <input class="form-check-input" type="checkbox" id="show_discount_badge" name="show_discount_badge">
                                                        <label class="form-check-label" for="show_discount_badge">
                                                            Show Discount Badge
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="show_popular_badge" name="show_popular_badge">
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
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="features[]" placeholder="Feature 1">
                                                <button type="button" class="btn btn-danger" onclick="removeFeature(this)">
                                                    <x-icon name="delete" size="16" />
                                                </button>
                                            </div>
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

        // Dynamic feature fields
        function addFeature() {
            const container = document.getElementById('features-container');
            const count = container.children.length + 1;
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="text" class="form-control" name="features[]" placeholder="Feature ${count}">
                <button type="button" class="btn btn-danger" onclick="removeFeature(this)">
                    <x-icon name="delete" size="16" />
                </button>
            `;
            container.appendChild(div);
        }

        function removeFeature(button) {
            if (document.getElementById('features-container').children.length > 1) {
                button.parentElement.remove();
            }
        }

        // Initialize the form with one empty feature field
        document.addEventListener('DOMContentLoaded', function() {
            addFeature();
        });
    </script>
@endpush
