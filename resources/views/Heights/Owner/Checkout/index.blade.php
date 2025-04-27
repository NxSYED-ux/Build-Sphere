@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Checkout']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Dashboard']" />
    <x-error-success-model />

@endsection

@push('scripts')



@endpush
