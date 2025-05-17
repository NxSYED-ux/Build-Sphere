@extends('layouts.app')

@section('title', $membership->name . ' - Membership Details')

@section('styles')
    <style>
        .text-gradient-primary {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .card {
            border-radius: 0.75rem;
            border: none;
        }

        .table th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .table td {
            vertical-align: middle;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
            transition: all 0.2s ease;
            background-color: var(--body-background-color) !important;
        }

        .badge.bg-light {
            padding: 0.35em 0.65em;
            background-color: #f1f5f9;
            color: var(--main-text-color) !important;
        }

        .badge.text-white {
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: #fff;
        }

        .vr {
            border-left: 2px solid #dee2e6;
            height: 100%;
        }

        .membership-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .featured-badge {
            position: absolute;
            top: 10px;       /* Adjust as needed */
            right: 10px;     /* Adjust as needed */
            z-index: 1;      /* Ensures badge stays on top of image */
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.memberships.index'), 'label' => 'Memberships'],
            ['url' => '', 'label' => 'Details']
        ]"
    />
    <x-Owner.side-navbar :openSections="['Memberships']" />
    <x-error-success-model />

    <div id="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="container pt-5 pb-3 mt-2">

                        <div class="row mb-2">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h1 class="display-6 fw-bold text-gradient-primary mb-2">{{ $membership->name }}</h1>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-{{ $membership->status === 'Published' ? 'success' : ($membership->status === 'Draft' ? 'warning' : 'secondary') }} text-white">
                                                {{ $membership->status }}
                                            </span>
                                            <span class="badge bg-primary">
                                                {{ $membership->category }}
                                            </span>
                                            @if($membership->mark_as_featured)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bx bx-star me-1"></i> Featured
                                                </span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('owner.memberships.edit', $membership->id) }}" class="btn btn-primary px-4">
                                            Edit
                                        </a>
                                        <a href="{{ route('owner.memberships.index') }}" class="btn btn-secondary px-4">
                                            Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-5">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-0  ">
                                        <img src="{{ asset($membership->image) }}" alt="Membership Image" class="img-fluid w-100 h-100 rounded-3 " style="object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold text-muted">Building</label>
                                                <p class="fw-bold">{{ $membership->building->name ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold text-muted">Unit</label>
                                                <p class="fw-bold">{{ $membership->unit->unit_name ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold text-muted">Duration</label>
                                                <p class="fw-bold">{{ $membership->duration_months }} months</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold text-muted">Scans per Day</label>
                                                <p class="fw-bold">{{ $membership->scans_per_day }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold text-muted">Price</label>
                                                <p class="fw-bold">{{ number_format($membership->price, 2) }} {{ $membership->currency }}</p>
                                            </div>
                                            @if($membership->original_price)
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold text-muted">Original Price</label>
                                                    <p class="fw-bold text-decoration-line-through">{{ number_format($membership->original_price, 2) }} {{ $membership->currency }}</p>
                                                </div>
                                            @endif
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold text-muted">URL</label>
                                                <p class="fw-bold">
                                                    @if($membership->url)
                                                        <a href="{{ $membership->url }}" target="_blank" class="text-primary">
                                                            <i class="fas fa-external-link-alt mr-1" style="margin-right: 10px; font-size: 0.8rem;"></i>Visit
                                                        </a>
                                                    @else
                                                        N/A
                                                    @endif
{{--                                                    <a href="{{ $membership->url }}" target="_blank">{{ $membership->url }}</a>--}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Membership Description -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header border-bottom-0 pt-3 pb-0">
                                        <h5 class="fw-semibold mb-0 d-flex align-items-center" style="color: var(--main-text-color);">
                                            <i class="bx bx-file text-primary me-2"></i> Membership Description
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="p-3 rounded shadow-sm" style="background-color: var(--body-background-color) !important;">
                                            <p class="mb-0">{{ $membership->description ?? 'No description provided.' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Users -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header border-bottom-0 py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="fw-semibold mb-0 d-flex align-items-center" style="color: var(--main-text-color);">
                                                <i class="bx bx-user text-primary me-2"></i> Subscribed Users
                                            </h5>
                                            <span class="badge bg-primary rounded-pill" style="color: #fff !important;">
                                                {{ $membership->membershipUsers->count() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        @if($membership->membershipUsers->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th width="5%" class="ps-4">#</th>
                                                        <th width="20%">Name</th>
                                                        <th width="20%">Email</th>
                                                        <th width="20%">Contact</th>
                                                        <th width="20%">Cnic</th>
                                                        <th width="20%">City</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="border">
                                                    @foreach($membership->membershipUsers as $index => $membershipUser)
                                                        <tr>
                                                            <td class="ps-4">{{ $index + 1 }}</td>
                                                            <td>{{ $membershipUser->user->name ?? 'N/A' }}</td>
                                                            <td>{{ $membershipUser->user->email ?? 'N/A' }}</td>
                                                            <td>{{ $membershipUser->user->phone_no ?? 'N/A' }}</td>
                                                            <td>{{ $membershipUser->user->cnic ?? 'N/A' }}</td>
                                                            <td>{{ $membershipUser->user->address->city ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <div class="rounded-circle px-4 py-1 d-inline-block mb-3">
                                                    <i class="bx bx-user-x" style="font-size: 2rem;"></i>
                                                </div>
                                                <h6 class="fw-semibold">No subscribed users</h6>
                                                <p class="small mb-0">This membership hasn't been subscribed to by any users yet</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
