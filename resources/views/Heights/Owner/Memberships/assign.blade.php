@extends('layouts.app')

@section('title', 'Assign Membership')

@push('styles')
    <style>
        :root {
            --primary: #6C5CE7;
            --primary-light: #A29BFE;
            --secondary: #00CEFF;
            --dark: #2D3436;
            --light: #F5F6FA;
            --success: #00B894;
            --warning: #FDCB6E;
            --danger: #D63031;
            --card-bg: #FFFFFF;
            --border: #DFE6E9;
        }

        #main {
            margin-top: 45px;
        }

        .glass-container {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            background-color: var(--sidenavbar-body-color) !important;
            transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .card-header {
            padding: 1.5rem;
            background-color: var(--sidenavbar-body-color);
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .card-title i {
            margin-right: 12px;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--color-blue), var(--primary-light));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-select {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .user-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
            outline: none;
        }

        .user-avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .grid-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .grid-card {
            background-color: var(--body-background-color);
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 10px;
            align-items: center;
        }

        .detail-label {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .detail-value {
            font-weight: 500;
            color: var(--sidenavbar-text-color);
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .grid-details {
                grid-template-columns: 1fr;
            }

            .detail-grid {
                grid-template-columns: 1fr;
                gap: 5px;
            }

            .detail-label {
                margin-bottom: 0;
            }
        }

        .user-details {
            background-color: var(--body-background-color);
            border-radius: 12px;
            padding: 1.2rem .5rem;
            margin-top: 1rem;
        }

        .user-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.78rem;
        }

        .user-detail-item {
            display: flex;
            flex-direction: column;
            padding: 0.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .user-detail-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            opacity: 0.7;
            margin-bottom: 0.25rem;
        }

        .user-detail-value {
            font-weight: 500;
            color: var(--sidenavbar-text-color);
            word-break: break-word;
            min-height: 1.5rem;
        }

        @media (max-width: 768px) {
            .user-details-grid {
                grid-template-columns: 1fr;
            }

            .user-detail-item {
                padding: 0.5rem;
            }
        }

        .price-section {
            margin: 0 .5rem;
            padding-top: 1.5rem;
        }

        .price-grid {
            display: grid;
            grid-template-columns: 120px 1fr;
            align-items: center;
            gap: 1rem;
        }

        .price-label {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            opacity: 0.7;
            font-size: 1rem;
        }

        .price-value {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .price-display {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--color-blue);
            line-height: 1.2;
        }

        .original-price {
            text-decoration: line-through;
            color: var(--sidenavbar-text-color);
            opacity: 0.5;
            font-size: 1.1rem;
        }

        .discount-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background: linear-gradient(135deg, var(--success), #55EFC4);
            color: white;
            font-weight: 700;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .price-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .price-value {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--color-blue), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.5rem;
        }

        .empty-state-text {
            color: var(--sidenavbar-text-color);
            opacity: 0.7;
            font-size: 1rem;
        }

        .membership-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: .2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .membership-highlight {
            background: linear-gradient(135deg, rgba(108, 92, 231, 0.05), rgba(0, 206, 255, 0.05));
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .membership-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--color-blue), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--warning), #FFEAA7);
            color: #2D3436;
            font-weight: 700;
            border-radius: 20px;
            font-size: 0.75rem;
            box-shadow: 0 4px 15px rgba(253, 203, 110, 0.3);
        }

        .price-display {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--color-blue);
        }

        .original-price {
            text-decoration: line-through;
            color: var(--sidenavbar-text-color);
            opacity: 0.5;
            font-size: 1.1rem;
            margin-left: 0.5rem;
        }

        .discount-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background: linear-gradient(135deg, var(--success), #55EFC4);
            color: white;
            font-weight: 700;
            border-radius: 20px;
            font-size: 0.75rem;
            margin-left: 0.75rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-weight: 700;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .status-published {
            background: linear-gradient(135deg, var(--success), #55EFC4);
            color: white;
        }

        .status-draft {
            background-color: rgba(45, 52, 54, 0.1);
            color: var(--sidenavbar-text-color);
        }

        .status-non-renewable {
            background: linear-gradient(135deg, var(--warning), #FFEAA7);
            color: var(--sidenavbar-text-color);
        }

        .submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 2.5rem;
            background: linear-gradient(135deg, var(--color-blue), var(--primary-light));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.3);
            margin: 0.1rem auto 0;
            width: 100%;
            /*max-width: 300px;*/
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(108, 92, 231, 0.4);
        }

        .submit-btn:disabled {
            background: linear-gradient(135deg, #B2B2B2, #D8D8D8);
            box-shadow: none;
            transform: none;
            cursor: not-allowed;
        }

        .loading-spinner {
            display: inline-block;
            width: 1.25rem;
            height: 1.25rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.75rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.75rem;
            }

            .detail-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .detail-label {
                margin-bottom: -0.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.memberships.index'), 'label' => 'Memberships'],
            ['url' => '', 'label' => 'Assign Membership']
        ]"
    />
    <x-Owner.side-navbar :openSections="['Memberships']" />
    <x-error-success-model />

    <div id="main">
        <div class="container my-3 px-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 >Assign Membership</h4>
                <a href="{{ route('owner.memberships.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card glass-container">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-user-tag"></i> Select User
                            </h2>
                        </div>
                        <div class="card-body">
                            <select class="user-select form-select mb-4" id="user_id" name="user_id" required>
                                <option value="">Select a user to assign membership</option>
                                @if(!empty($availableUsers) && count($availableUsers))
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}" data-email="{{ $user->email }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No available users found</option>
                                @endif
                            </select>

                            <div class="card glass-container mt-3">
                                <div class="card-header" style="background-color: var(--body-background-color) !important;">
                                    <h2 class="card-title">
                                        <i class="fas fa-user-circle"></i> User Details
                                    </h2>
                                </div>
                                <div class="card-body" style="background-color: var(--body-background-color) !important;">
                                    <div id="user-empty-state" class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-user-plus"></i>
                                        </div>
                                        <h3 class="empty-state-title">Select a User</h3>
                                        <p class="empty-state-text">Choose a user from the dropdown to view details</p>
                                    </div>

                                    <div id="user-details-content" style="display: none;">
                                        <div class="text-center mb-4">
                                            <img id="user_avatar" src="{{ asset('img/placeholder-profile.png') }}" class="user-avatar" alt="User Avatar">
                                        </div>
                                        <div class="user-details">
                                            <div class="user-details-grid">
                                                <div class="user-detail-item">
                                                    <span class="user-detail-label">Full Name</span>
                                                    <span class="user-detail-value" id="user_name">-</span>
                                                </div>
                                                <div class="user-detail-item">
                                                    <span class="user-detail-label">Email</span>
                                                    <span class="user-detail-value" id="user_email">-</span>
                                                </div>
                                                <div class="user-detail-item">
                                                    <span class="user-detail-label">Phone</span>
                                                    <span class="user-detail-value" id="user_contact">-</span>
                                                </div>
                                                <div class="user-detail-item">
                                                    <span class="user-detail-label">CNIC</span>
                                                    <span class="user-detail-value" id="user_cnic">-</span>
                                                </div>
                                                <div class="user-detail-item">
                                                    <span class="user-detail-label">Gender</span>
                                                    <span class="user-detail-value" id="user_gender">-</span>
                                                </div>
                                                <div class="user-detail-item">
                                                    <span class="user-detail-label">City</span>
                                                    <span class="user-detail-value" id="user_city">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card glass-container">
                        <div class="card-header d-flex justify-content-between align-items-center" style="padding: 1.2rem 1.5rem">
                            <h2 class="card-title">
                                <i class="fas fa-id-card-alt"></i> Membership Details
                            </h2>
                            <div class="">
                                @if($membership->status == 'Published')
                                    <span class="status-badge status-published">{{ $membership->status }}</span>
                                @elseif($membership->status == 'Draft')
                                    <span class="status-badge status-draft">{{ $membership->status }}</span>
                                @elseif($membership->status == 'Non Renewable')
                                    <span class="status-badge status-non-renewable">{{ $membership->status }}</span>
                                @else
                                    <span class="status-badge">{{ $membership->status }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="{{ asset($membership->image) }}" class="membership-image" alt="Membership Image">
                            </div>

                            <div class="membership-highlight">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="membership-name">{{ $membership->name }}</h3>
                                    @if($membership->mark_as_featured)
                                        <span class="feature-badge">
                                            <i class="fas fa-star me-1"></i> FEATURED
                                        </span>
                                    @endif
                                </div>
                                <p class="mb-0">{{ $membership->description }}</p>
                            </div>

                            <div class="grid-details">
                                <div class="grid-card">
                                    <div class="detail-grid">
                                        <div class="detail-label">Category</div>
                                        <div class="detail-value">
                                            {{ $membership->category }}
                                            @if($membership->category === 'Other')
                                                <div class="mt-1 small opacity-75">{{ $membership->type ?? 'N/A' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid-card">
                                    <div class="detail-grid">
                                        <div class="detail-label">Building</div>
                                        <div class="detail-value">{{ $membership->building->name ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="grid-card">
                                    <div class="detail-grid">
                                        <div class="detail-label">Unit</div>
                                        <div class="detail-value">{{ $membership->unit->unit_name ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="grid-card">
                                    <div class="detail-grid">
                                        <div class="detail-label">Duration</div>
                                        <div class="detail-value">{{ $membership->duration_months }} months</div>
                                    </div>
                                </div>

                                <div class="grid-card">
                                    <div class="detail-grid">
                                        <div class="detail-label">Scans/Day</div>
                                        <div class="detail-value">{{ $membership->scans_per_day }}</div>
                                    </div>
                                </div>

                                <div class="grid-card">
                                    <div class="detail-grid">
                                        <div class="detail-label">URL</div>
                                        <div class="detail-value">
                                            @if($membership->url)
                                                <a href="{{ $membership->url }}" target="_blank" class="text-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Visit Link
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="price-section">
                                <div class="detail-grid price-grid">
                                    <div class="price-label">Price</div>
                                    <div class="price-value">
                                        <span class="price-display">{{ $membership->currency }} {{ number_format($membership->price, 2) }}</span>
                                        @if($membership->original_price && $membership->original_price > $membership->price)
                                            <span class="original-price">{{ $membership->currency }} {{ number_format($membership->original_price, 2) }}</span>
                                            <span class="discount-badge">
                                                {{ number_format(100 - ($membership->price / $membership->original_price * 100), 0) }}% OFF
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <form action="{{ route('owner.memberships.assign') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                    <input type="hidden" name="user_id" id="form_user_id" value="">

                    <button type="submit" class="submit-btn" id="assignBtn" disabled>
                        <span id="button-text">
                            <i class="fas fa-user-check me-2"></i> Assign Membership
                        </span>
                        <span id="button-loading" style="display: none;">
                            <span class="loading-spinner"></span> Processing...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const userIdSelect = document.getElementById('user_id');
            const formUserId = document.getElementById('form_user_id');
            const userDetailsContent = document.getElementById('user-details-content');
            const userEmptyState = document.getElementById('user-empty-state');
            const assignBtn = document.getElementById('assignBtn');
            const buttonText = document.getElementById('button-text');
            const buttonLoading = document.getElementById('button-loading');

            // Handle user selection change
            userIdSelect.addEventListener('change', function() {
                const userId = this.value;
                formUserId.value = userId;

                if (userId) {
                    // Show loading state
                    userEmptyState.style.display = 'none';
                    userDetailsContent.style.display = 'block';
                    document.getElementById('user_name').innerHTML = '<span class="loading-spinner"></span> Loading...';
                    document.getElementById('user_email').textContent = '-';
                    document.getElementById('user_contact').textContent = '-';
                    document.getElementById('user_cnic').textContent = '-';
                    document.getElementById('user_gender').textContent = '-';
                    document.getElementById('user_city').textContent = '-';
                    assignBtn.disabled = true;
                    buttonText.style.display = 'inline-flex';
                    buttonLoading.style.display = 'none';

                    // Fetch user details
                    fetchUserDetails(userId);
                } else {
                    userEmptyState.style.display = 'block';
                    userDetailsContent.style.display = 'none';
                    assignBtn.disabled = true;
                }
            });

            function fetchUserDetails(userId) {
                if (!userId) return;

                fetch(`{{ route('users.show', ':id') }}`.replace(':id', userId), {
                    method: "GET",
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const user = data.user;
                        const placeholderImage = "{{ asset('img/placeholder-profile.png') }}";
                        const baseAssetPath = "{{ asset('/') }}";

                        // Update user details
                        document.getElementById('user_name').textContent = user.name || '-';
                        document.getElementById('user_email').textContent = user.email || '-';
                        document.getElementById('user_contact').textContent = user.phone_no || '-';
                        document.getElementById('user_cnic').textContent = user.cnic || '-';
                        document.getElementById('user_gender').textContent = user.gender || '-';
                        document.getElementById('user_city').textContent = user.address?.city || '-';

                        // Update user image
                        const avatar = document.getElementById('user_avatar');
                        avatar.src = user.picture ? baseAssetPath + user.picture : placeholderImage;

                        // Enable assign button
                        assignBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error("Error fetching user details:", error);

                        // Show error state
                        userEmptyState.style.display = 'block';
                        userDetailsContent.style.display = 'none';
                        assignBtn.disabled = true;

                        // Update empty state with error message
                        const emptyStateIcon = userEmptyState.querySelector('.empty-state-icon');
                        const emptyStateTitle = userEmptyState.querySelector('.empty-state-title');
                        const emptyStateText = userEmptyState.querySelector('.empty-state-text');

                        emptyStateIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
                        emptyStateTitle.textContent = 'Error Loading User';
                        emptyStateText.textContent = 'Please try selecting the user again';
                    });
            }

            // Form submission handling
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (assignBtn.disabled) {
                    e.preventDefault();
                    return;
                }

                buttonText.style.display = 'none';
                buttonLoading.style.display = 'inline-flex';
                assignBtn.disabled = true;
            });
        });
    </script>
@endpush
