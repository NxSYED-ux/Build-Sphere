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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Transaction Details</h4>
                    </div>

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Basic Information</h5>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <strong>Transaction ID:</strong> {{ $transaction['transaction_id'] }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Title:</strong> {{ $transaction['title'] }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Type:</strong> {{ ucfirst($transaction['type']) }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Amount:</strong> {{ $transaction['price'] }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Status:</strong>
                                        <span class="badge bg-{{ $transaction['status'] === 'completed' ? 'success' : ($transaction['status'] === 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($transaction['status']) }}
                                    </span>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-md-6">
                                <h5>Additional Details</h5>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <strong>Date:</strong> {{ $transaction['created_at'] }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Payment Method:</strong> {{ ucfirst($transaction['payment_method']) }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Subscription:</strong>
                                        {{ $transaction['is_subscription'] ? 'Yes' : 'No' }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if($transaction['is_subscription'])
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Subscription Details</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <strong>Start Date:</strong> {{ $transaction['subscription_details']['start_date'] ?? 'N/A' }}
                                        </li>
                                        <li class="list-group-item">
                                            <strong>End Date:</strong> {{ $transaction['subscription_details']['end_date'] ?? 'N/A' }}
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Billing Cycle:</strong> {{ ucfirst($transaction['subscription_details']['billing_cycle'] ?? 'N/A') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if($source)
                            <div class="card">
                                <div class="card-header">
                                    <h5>Source Information</h5>
                                </div>
                                <div class="card-body">

                                    <h3>Source Name: {{ $source_name }}</h3>
                                    @php
                                        $localName = $source_name ?? 'unknown';
                                        $localSource = $source ?? [];
                                    @endphp

                                    <x-transaction-component
                                            :source="$source"
                                            :source_name="$source_name"
                                    />


                                @if(is_object($source))
                                        <pre>{{ print_r($source->toArray(), true) }}</pre>
                                    @else
                                        <pre>{{ print_r($source, true) }}</pre>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($nested_source)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Related Source</h5>
                                </div>
                                <div class="card-body">
                                    @if(is_object($nested_source))
                                        <pre>{{ print_r($nested_source->toArray(), true) }}</pre>
                                    @else
                                        <pre>{{ print_r($nested_source, true) }}</pre>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('owner.finance.index') }}" class="btn btn-primary">
                                Back to Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')


@endpush
