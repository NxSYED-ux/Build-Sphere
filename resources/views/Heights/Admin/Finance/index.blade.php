@extends('layouts.app')

@section('title', 'Finance')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Finance']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Finance']" />
    <x-error-success-model />


    <div id="main">

        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Finance</h3>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " style="overflow-x: auto;">
                                        <div class="row">
                                            @foreach($history as $item)
                                                <x-transaction-card :transaction="$item" route-name="finance.show"  />
                                            @endforeach
                                        </div>
                                        @if ($transactions)
                                            <div class="mt-3 custom-pagination-wrapper">
                                                {{ $transactions->links('pagination::bootstrap-5') }}
                                            </div>
                                        @endif

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


@endpush
