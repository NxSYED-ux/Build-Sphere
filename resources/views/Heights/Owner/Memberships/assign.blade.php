@extends('layouts.app')

@section('title', 'Assign Membership')

@push('styles')
    <style>
        #main {
            margin-top: 55px;
        }
        .details-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .details-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .user-details-card .card-header {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .membership-details-card .card-header {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
        }
        table.table-details th {
            width: 30%;
            padding-left: 0;
            color: #555;
            font-weight: 500;
        }
        table.table-details td {
            width: 70%;
            color: #333;
        }
        #user_avatar {
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #777;
        }
        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        .highlight-box {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .feature-badge {
            background-color: #ffc107;
            color: #212529;
        }
        .price-display {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .original-price {
            text-decoration: line-through;
            color: #999;
        }
        .discount-badge {
            background-color: #28a745;
            color: white;
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            margin-left: 0.5rem;
        }

        .table-details th{
            padding-left: 5px !important;
        }

        .section-header {
            color: var(--sidenavbar-text-color);
            font-weight: 500;
            margin-bottom: 15px;
            margin-right: 3px;
            display: flex;
            align-items: center;
        }
        .section-header i {
            margin-right: 10px;
            font-size: 1.2em;
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <h3 class="mb-1">Assign Membership</h3>
                    </div>

                    <div class="card shadow-sm">

                        <div class="card-body">
                            <form action="{{ route('owner.memberships.assign') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="membership_id" value="{{ $membership->id }}">

                                <div class="row">
                                    <!-- Left Side - User Selection and Details -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5 class="section-header">
                                                <i class='bx bxs-user-detail'></i> Select User
                                            </h5>
{{--                                            <label for="user_id"><i class="fas fa-user mr-1"></i> Select User</label>--}}
                                            <select class="form-select" id="user_id" name="user_id" required>
                                                <option value="">Select a user</option>
                                                @foreach($availableUsers as $user)
                                                    <option value="{{ $user->id }}" data-email="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="card mt-3 user-details-card details-card">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="fas fa-user-circle" style="margin-right: 10px; font-size: 1.2rem;"></i>User Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="user-empty-state" class="empty-state">
                                                    <i class="fas fa-user-slash"></i>
                                                    <h5>No User Selected</h5>
                                                    <p class="mb-0">Select a user from the dropdown above to view details</p>
                                                </div>

                                                <div id="user-details-content" style="display: none;">
                                                    <div class="text-center mb-3">
                                                        <img id="user_avatar" src="{{ asset('img/placeholder-profile.png') }}" class="rounded-circle" width="120" height="120" alt="User Image">
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-details">
                                                            <tr>
                                                                <th>Name:</th>
                                                                <td id="user_name">-</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Email:</th>
                                                                <td id="user_email">-</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Contact:</th>
                                                                <td id="user_contact">-</td>
                                                            </tr>
                                                            <tr>
                                                                <th>CNIC:</th>
                                                                <td id="user_cnic">-</td>
                                                            </tr>
                                                            <tr>
                                                                <th>City:</th>
                                                                <td id="user_city">-</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Side - Membership Details -->
                                    <div class="col-md-6">
                                        <div class="card membership-details-card details-card">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="fas fa-id-card" style="margin-right: 10px; font-size: 1.2rem;"></i>Membership Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="text-center mb-3">
                                                    <img src="{{ asset($membership->image) }}" class="img-fluid rounded" style="max-height: 120px;" alt="Membership Image">
                                                </div>

                                                <div class="highlight-box">
                                                    <h5 class="mb-2 text-dark">{{ $membership->name }}</h5>
                                                    <p class="text-muted mb-2">{{ $membership->description }}</p>
                                                    @if($membership->mark_as_featured)
                                                        <span class="badge feature-badge mb-2"><i class="fas fa-star mr-1"></i>Featured</span>
                                                    @endif
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-sm table-details">
                                                        <tr>
                                                            <th>Category:</th>
                                                            <td>
                                                                {{ $membership->category }}
                                                                @if($membership->category === 'Other')
                                                                    <small class="text-muted">({{ $membership->type ?? 'N/A' }})</small>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Building:</th>
                                                            <td>{{ $membership->building->name ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Unit:</th>
                                                            <td>{{ $membership->unit->unit_name ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Duration:</th>
                                                            <td>{{ $membership->duration_months }} months</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Scans/Month:</th>
                                                            <td>{{ $membership->scans_per_day }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status:</th>
                                                            <td>
                                                                <span class="badge badge-{{
                                                                    $membership->status == 'Published' ? 'success' :
                                                                    ($membership->status == 'Draft' ? 'secondary' :
                                                                    ($membership->status == 'Non Renewable' ? 'warning' : 'dark'))
                                                                }}">
                                                                    {{ $membership->status }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Price:</th>
                                                            <td>
                                                                <span class="price-display">{{ $membership->currency }} {{ number_format($membership->price, 2) }}</span>
                                                                @if($membership->original_price && $membership->original_price > $membership->price)
                                                                    <span class="original-price">{{ $membership->currency }} {{ number_format($membership->original_price, 2) }}</span>
                                                                    <span class="discount-badge">
                                                                        {{ number_format(100 - ($membership->price / $membership->original_price * 100), 0) }}% OFF
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>URL:</th>
                                                            <td>
                                                                @if($membership->url)
                                                                    <a href="{{ $membership->url }}" target="_blank" class="text-primary">
                                                                        <i class="fas fa-external-link-alt mr-1"></i>Visit
                                                                    </a>
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12 text-right">
                                        <a href="{{ route('owner.memberships.index') }}" class="btn btn-outline-secondary mr-2">
                                            <i class="fas fa-times mr-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="assignBtn" disabled>
                                            <i class="fas fa-user-check mr-1"></i> Assign Membership
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Handle user selection change
            document.getElementById('user_id').addEventListener('change', function() {
                const userId = this.value;
                const userDetailsContent = document.getElementById('user-details-content');
                const userEmptyState = document.getElementById('user-empty-state');
                const assignBtn = document.getElementById('assignBtn');

                if (userId) {
                    // Show loading state
                    userEmptyState.style.display = 'none';
                    userDetailsContent.style.display = 'block';
                    document.getElementById('user_name').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    document.getElementById('user_email').textContent = '-';
                    document.getElementById('user_contact').textContent = '-';
                    document.getElementById('user_cnic').textContent = '-';
                    document.getElementById('user_city').textContent = '-';
                    assignBtn.disabled = true;

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
                        const assignBtn = document.getElementById('assignBtn');
                        const placeholderImage = "{{ asset('img/placeholder-profile.png') }}";
                        const baseAssetPath = "{{ asset('/') }}";

                        // Update user details
                        document.getElementById('user_name').textContent = user.name || '-';
                        document.getElementById('user_email').textContent = user.email || '-';
                        document.getElementById('user_contact').textContent = user.phone_no || '-';
                        document.getElementById('user_cnic').textContent = user.cnic || '-';
                        document.getElementById('user_city').textContent = user.address?.city || '-';

                        // Update user image
                        const avatar = document.getElementById('user_avatar');
                        avatar.src = user.picture ? baseAssetPath + user.picture : placeholderImage;

                        // Enable assign button
                        assignBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error("Error fetching user details:", error);
                        toastr.error('An error occurred while fetching user details');
                        document.getElementById('user-empty-state').style.display = 'block';
                        document.getElementById('user-details-content').style.display = 'none';
                        document.getElementById('assignBtn').disabled = true;
                    });
            }
        });
    </script>
@endpush
