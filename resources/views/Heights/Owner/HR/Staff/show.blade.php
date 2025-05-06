@extends('layouts.app')

@section('title', 'Staff Details')

@push('styles')
    <style>
        #main {
            margin-top: 45px;
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
        ['url' => '', 'label' => 'Staff']
    ]" />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Staff']" />

    <!-- Error/Success Modal -->
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3 class="mb-1">Staff Members</h3>
                                </div>

                                <a href="#" class="btn btn-primary" title="Promote To Manager">
                                    <i class="fas fa-user-plus me-2"></i> Promote staff to Manager
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('scripts')
@endpush
