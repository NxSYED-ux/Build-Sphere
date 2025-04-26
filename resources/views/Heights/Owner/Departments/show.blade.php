@extends('layouts.app')

@section('title', $department->name . ' Department')

@push('styles')
    <style>
        :root {
            /* Custom Elegant Color Palette */
            --sage-green: #87bc8d;
            --deep-teal: #0b5351;
            --warm-taupe: #c4a381;
            --soft-clay: #d7b29d;
            --mist-blue: #a5c4d4;
            --pale-blush: #e8c7c8;
            --dark-charcoal: #33312e;
            --light-ivory: #f8f5f2;
            --soft-gray: #e0ddd9;
        }

        /* Main Content Styling */
        #main {
            margin-top: 45px;
            transition: all 0.3s;
            background: var(--light-ivory);
        }

        /* Department Header - Hero Section */
        .department-hero {
            background: linear-gradient(135deg, var(--deep-teal) 0%, var(--sage-green) 100%);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }

        .department-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            z-index: -1;
        }

        .department-title {
            font-weight: 700;
            margin-bottom: 0.75rem;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            display: inline-block;
            font-family: 'Playfair Display', serif;
            color: var(--light-ivory);
        }

        .department-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--warm-taupe);
            border-radius: 2px;
        }

        .department-description {
            font-size: 1.1rem;
            line-height: 1.7;
            max-width: 800px;
            opacity: 0.9;
            margin-bottom: 1.5rem;
            color: rgba(255,255,255,0.9);
        }

        .department-meta {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.85);
        }

        .meta-icon {
            font-size: 1.1rem;
            color: var(--warm-taupe);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-edit {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .btn-edit:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-delete {
            background: rgba(199, 84, 80, 0.15);
            border: 1px solid rgba(199, 84, 80, 0.3);
            color: white;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .btn-delete:hover {
            background: rgba(199, 84, 80, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-back {
            background: rgba(255,255,255,0.9);
            color: var(--deep-teal);
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-back:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Staff Team Section */
        .team-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out 0.2s both;
            border: 1px solid var(--soft-gray);
        }

        .section-header {
            padding: 1.75rem 2rem;
            border-bottom: 1px solid var(--soft-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
        }

        .section-title {
            font-weight: 600;
            color: var(--dark-charcoal);
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: 'Playfair Display', serif;
        }

        .section-title-icon {
            color: var(--sage-green);
            font-size: 1.4rem;
        }

        .team-count {
            background: var(--sage-green);
            color: white;
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .export-btn {
            background: var(--deep-teal);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.65rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            font-weight: 500;
        }

        .export-btn:hover {
            background: var(--sage-green);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(11, 83, 81, 0.15);
        }

        /* Team Members Grid */
        .team-members {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
        }

        .member-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid var(--soft-gray);
            position: relative;
        }

        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--mist-blue);
        }

        .member-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--soft-gray);
            background: linear-gradient(to bottom, white 0%, var(--light-ivory) 100%);
        }

        .member-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 0 auto 1rem;
            transition: all 0.3s ease;
        }

        .member-card:hover .member-avatar {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: var(--pale-blush);
        }

        .member-name {
            font-weight: 700;
            color: var(--dark-charcoal);
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
            font-family: 'Playfair Display', serif;
        }

        .member-position {
            color: var(--warm-taupe);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .member-details {
            padding: 1.25rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .detail-icon {
            color: var(--sage-green);
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .detail-text {
            font-size: 0.9rem;
            color: var(--dark-charcoal);
        }

        /* Empty State */
        .empty-team {
            padding: 4rem 2rem;
            text-align: center;
            grid-column: 1 / -1;
            background: var(--light-ivory);
            border-radius: 12px;
            margin: 1rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--mist-blue);
            margin-bottom: 1.5rem;
            opacity: 0.7;
        }

        .empty-title {
            font-weight: 600;
            color: var(--dark-charcoal);
            margin-bottom: 0.5rem;
            font-family: 'Playfair Display', serif;
        }

        .empty-text {
            color: var(--warm-taupe);
            max-width: 500px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        .btn-invite {
            background: var(--sage-green);
            color: white;
            border-radius: 50px;
            padding: 0.6rem 1.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
            border: none;
        }

        .btn-invite:hover {
            background: var(--deep-teal);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(135, 188, 141, 0.3);
        }

        /* Modal Styling */
        .modal-content {
            background: white;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            border: 1px solid var(--soft-gray);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--deep-teal) 0%, var(--sage-green) 100%);
            border-bottom: none;
            padding: 1.5rem;
            color: white;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            justify-content: center;
            font-family: 'Playfair Display', serif;
        }

        .modal-title-icon {
            font-size: 1.5rem;
            color: var(--warm-taupe);
        }

        .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .modal-body {
            padding: 2rem;
            background: var(--light-ivory);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-charcoal);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label-icon {
            color: var(--sage-green);
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--soft-gray);
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus {
            border-color: var(--sage-green);
            box-shadow: 0 0 0 0.25rem rgba(135, 188, 141, 0.1);
        }

        .modal-footer {
            border-top: 1px solid var(--soft-gray);
            padding: 1.25rem 2rem;
            background: white;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .department-hero {
                padding: 2rem 1.5rem;
            }

            .department-title {
                font-size: 1.8rem;
            }

            .team-members {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .department-hero {
                text-align: center;
            }

            .department-title::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .department-meta {
                justify-content: center;
            }

            .action-buttons {
                justify-content: center;
            }

            .section-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .team-members {
                grid-template-columns: 1fr;
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .department-hero {
                padding: 1.75rem 1.25rem;
            }

            .department-title {
                font-size: 1.6rem;
            }

            .department-description {
                font-size: 1rem;
            }

            .modal-body {
                padding: 1.5rem;
            }
        }
    </style>

    <!-- Include Playfair Display font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
    <!-- Top Navbar -->
    <x-Owner.top-navbar
        :searchVisible="false"
        :breadcrumbLinks="[
            ['url' => url('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.departments.index'), 'label' => 'Departments'],
            ['url' => '', 'label' => $department->name]
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Departments']" />
    <x-error-success-model />

    <div id="main">
        <section class="content mx-2 my-4">
            <div class="container-fluid">
                <!-- Department Hero Section -->
                <div class="department-hero">
                    <h1 class="department-title">{{ $department->name }}</h1>
                    <p class="department-description">
                        {{ $department->description ?? 'A dedicated team working in harmony to achieve excellence and create meaningful impact.' }}
                    </p>

                    <div class="department-meta">
                        <div class="meta-item">
                            <i class="fas fa-users meta-icon"></i>
                            <span>{{ $staffMembers->count() }} Team Members</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt meta-icon"></i>
                            <span>Established {{ $department->created_at->format('F Y') }}</span>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="#" class="btn-edit Owner-Department-Edit-Button" data-id="{{ $department->id }}">
                            <i class="fas fa-pen"></i> Edit Department
                        </a>

                        <form action="{{ route('owner.departments.destroy', $department->id) }}"
                              method="POST"
                              class="d-inline"
                              id="delete-department-form-{{ $department->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    class="btn-delete delete-department-btn"
                                    data-id="{{ $department->id }}">
                                <i class="fas fa-trash"></i> Delete Department
                            </button>
                        </form>

                        <a href="{{ route('owner.departments.index') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Back to Departments
                        </a>
                    </div>
                </div>

                <!-- Team Members Section -->
                <div class="team-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-users section-title-icon"></i>
                            Our Team
                            <span class="team-count">{{ $staffMembers->count() }}</span>
                        </h2>

                        <div class="dropdown">
                            <button class="export-btn dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download"></i> Export Team
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li><button class="dropdown-item" type="button" id="copyButton"><i class="fas fa-copy me-2"></i>Copy</button></li>
                                <li><button class="dropdown-item" type="button" id="csvButton"><i class="fas fa-file-csv me-2"></i>CSV</button></li>
                                <li><button class="dropdown-item" type="button" id="excelButton"><i class="fas fa-file-excel me-2"></i>Excel</button></li>
                                <li><button class="dropdown-item" type="button" id="pdfButton"><i class="fas fa-file-pdf me-2"></i>PDF</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item" type="button" id="printButton"><i class="fas fa-print me-2"></i>Print</button></li>
                            </ul>
                        </div>
                    </div>

                    @if($staffMembers->count() > 0)
                        <div class="team-members">
                            @foreach($staffMembers as $staffMember)
                                <div class="member-card">
                                    <div class="member-header">
                                        <img src="{{ $staffMember->user->picture ? asset($staffMember->user->picture) : asset('img/placeholder-profile.png') }}"
                                             alt="{{ $staffMember->user->name }}"
                                             class="member-avatar"
                                             onerror="this.src='{{ asset('img/placeholder-profile.png') }}'">
                                        <h3 class="member-name">{{ $staffMember->user->name }}</h3>
                                        <p class="member-position">Team Member</p>
                                    </div>
                                    <div class="member-details">
                                        <div class="detail-item">
                                            <i class="fas fa-envelope detail-icon"></i>
                                            <div class="detail-text">
                                                <a href="mailto:{{ $staffMember->user->email }}" style="color: var(--deep-teal);">{{ $staffMember->user->email }}</a>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-phone detail-icon"></i>
                                            <div class="detail-text">
                                                {{ $staffMember->user->phone_no ?? 'Not provided' }}
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-map-marker-alt detail-icon"></i>
                                            <div class="detail-text">
                                                {{ $staffMember->user->address ? $staffMember->user->address->city : 'Location not set' }}
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-building detail-icon"></i>
                                            <div class="detail-text">
                                                {{ $staffMember->building ? $staffMember->building->name : 'Building not assigned' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-team">
                            <div class="empty-icon">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <h3 class="empty-title">Let's Build Your Team</h3>
                            <p class="empty-text">
                                This department is like a garden waiting to bloom. Invite your first team member to begin cultivating something beautiful together.
                            </p>
                            <button class="btn-invite">
                                <i class="fas fa-user-plus"></i> Invite First Member
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModel" tabindex="-1" aria-labelledby="editDepartmentModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModelLabel">
                        <i class="fas fa-edit modal-title-icon"></i> Edit Department
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editDepartmentForm" action="{{ route('owner.departments.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_department_id">
                    <input type="hidden" name="updated_at" id="edit_updated_at">

                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="edit_department_name" class="form-label">
                                <i class="fas fa-tag form-label-icon"></i> Department Name
                            </label>
                            <input type="text"
                                   name="edit_name"
                                   id="edit_department_name"
                                   class="form-control @error('edit_name') is-invalid @enderror"
                                   value="{{ old('edit_name') }}"
                                   maxlength="50"
                                   placeholder="Enter department name"
                                   required>
                            @error('edit_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="edit_department_description" class="form-label">
                                <i class="fas fa-align-left form-label-icon"></i> Description
                            </label>
                            <textarea name="edit_description"
                                      id="edit_department_description"
                                      rows="5"
                                      class="form-control @error('edit_description') is-invalid @enderror"
                                      maxlength="250"
                                      placeholder="What makes this department unique?">{{ old('edit_description') }}</textarea>
                            @error('edit_description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                            <div class="form-text text-end mt-1" style="color: var(--warm-taupe);">
                                <span id="description-counter">0</span>/250 characters
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-color: var(--soft-gray); color: var(--dark-charcoal);">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn" style="background: var(--sage-green); color: white;">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables & Buttons -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Character counter for description
            const descriptionTextarea = document.getElementById('edit_department_description');
            const counterElement = document.getElementById('description-counter');

            if (descriptionTextarea && counterElement) {
                descriptionTextarea.addEventListener('input', function() {
                    counterElement.textContent = this.value.length;
                });
            }

            // Export button handlers
            document.getElementById('copyButton')?.addEventListener('click', function() {
                // Implement copy functionality
                console.log("Copy team data to clipboard");
                showToast('Team data copied to clipboard!', 'success');
            });

            document.getElementById('csvButton')?.addEventListener('click', function() {
                console.log("Export as CSV");
                showToast('Exporting team data as CSV...', 'info');
            });

            document.getElementById('excelButton')?.addEventListener('click', function() {
                console.log("Export as Excel");
                showToast('Exporting team data as Excel...', 'info');
            });

            document.getElementById('pdfButton')?.addEventListener('click', function() {
                console.log("Export as PDF");
                showToast('Exporting team data as PDF...', 'info');
            });

            document.getElementById('printButton')?.addEventListener('click', function() {
                window.print();
                showToast('Preparing team data for printing...', 'info');
            });

            // Delete department confirmation
            document.querySelectorAll('.delete-department-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const departmentId = this.getAttribute('data-id');
                    const departmentName = document.querySelector('.department-title').textContent;

                    Swal.fire({
                        title: 'Nurture or Let Go?',
                        html: `
                            <div style="text-align: center;">
                                <i class="fas fa-seedling" style="font-size: 4rem; color: var(--sage-green); margin-bottom: 1rem;"></i>
                                <p>You're about to remove the <strong>${departmentName}</strong> department.</p>
                                <p>Like pruning a garden, this will remove the structure but the individuals will continue to grow elsewhere.</p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonColor: 'var(--sage-green)',
                        cancelButtonColor: 'var(--warm-taupe)',
                        confirmButtonText: 'Yes, remove it',
                        cancelButtonText: 'No, keep it',
                        reverseButtons: true,
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'custom-swal-confirm',
                            cancelButton: 'custom-swal-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Transforming...',
                                html: 'Gently reorganizing our garden',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    document.getElementById(`delete-department-form-${departmentId}`).submit();
                                }
                            });
                        }
                    });
                });
            });

            // Edit department modal handling
            document.querySelectorAll('.Owner-Department-Edit-Button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    // Show loading state
                    Swal.fire({
                        title: 'Cultivating Details',
                        html: 'Tending to department information...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        background: 'var(--light-ivory)',
                        color: 'var(--dark-charcoal)'
                    });

                    fetch(`{{ route('owner.departments.edit', ':id') }}`.replace(':id', id), {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.close();

                            if (data.error) {
                                showToast(data.error, 'error');
                            } else {
                                document.getElementById("edit_department_id").value = data.department?.id || "";
                                document.getElementById("edit_department_name").value = data.department?.name || "";
                                document.getElementById("edit_department_description").value = data.department?.description || "";
                                document.getElementById("edit_updated_at").value = data.department?.updated_at || "";

                                // Update character counter
                                if (counterElement) {
                                    counterElement.textContent = data.department?.description?.length || 0;
                                }

                                const editModal = new bootstrap.Modal(document.getElementById("editDepartmentModel"));
                                editModal.show();

                                // Play a subtle animation when modal appears
                                const modalContent = document.querySelector('.modal-content');
                                modalContent.style.animation = 'fadeInUp 0.4s ease-out';
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Connection Lost',
                                text: 'The garden path could not be found. Please try again.',
                                icon: 'error',
                                confirmButtonColor: 'var(--sage-green)',
                                background: 'var(--light-ivory)',
                                color: 'var(--dark-charcoal)'
                            });
                            console.error('Error:', error);
                        });
                });
            });

            // Helper function to show toast notifications
            function showToast(message, type = 'success') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: 'var(--light-ivory)',
                    color: 'var(--dark-charcoal)',
                    iconColor: type === 'success' ? 'var(--sage-green)' : 'var(--soft-clay)',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: type,
                    title: message
                });
            }
        });
    </script>
@endpush
