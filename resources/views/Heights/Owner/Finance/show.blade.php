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
    <div class="card p-4">
        <h2>Transaction Details</h2>

        <p><strong>Title:</strong> {{ $transaction['title'] }}</p>
        <p><strong>Type:</strong> {{ ucfirst($transaction['type']) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($transaction['status']) }}</p>
        <p><strong>Price:</strong> {{ $transaction['price'] }}</p>
        <p><strong>Payment Method:</strong> {{ $transaction['payment_method'] ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $transaction['created_at'] }}</p>

        @if($transaction['is_subscription'])
            <h4>Subscription Details</h4>
            <p><strong>Start:</strong> {{ $transaction['subscription_details']['start_date'] ?? 'N/A' }}</p>
            <p><strong>End:</strong> {{ $transaction['subscription_details']['end_date'] ?? 'N/A' }}</p>
            <p><strong>Billing Cycle:</strong> {{ $transaction['subscription_details']['billing_cycle'] ?? 'N/A' }}</p>
        @endif

        @if($transaction['source'])
            <h4>Source Details ({{ $transaction['source']['type'] }})</h4>
            <ul>
                @foreach($transaction['source']['details'] as $key => $value)
                    <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection

@push('scripts')


@endpush
