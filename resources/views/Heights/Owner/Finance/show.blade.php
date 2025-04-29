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
    <div class="container py-4">
        <h2 class="mb-4">Transaction Details</h2>

        <div class="card shadow p-4 rounded-3">
            <dl class="row">
                <dt class="col-sm-4">Transaction ID</dt>
                <dd class="col-sm-8">{{ $transaction['transaction_id'] }}</dd>

                <dt class="col-sm-4">Title</dt>
                <dd class="col-sm-8">{{ $transaction['title'] }}</dd>

                <dt class="col-sm-4">Type</dt>
                <dd class="col-sm-8">{{ $transaction['type'] }}</dd>

                <dt class="col-sm-4">Price</dt>
                <dd class="col-sm-8">{{ $transaction['price'] }}</dd>

                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">{{ ucfirst($transaction['status']) }}</dd>

                <dt class="col-sm-4">Payment Method</dt>
                <dd class="col-sm-8">{{ $transaction['payment_method'] }}</dd>

                <dt class="col-sm-4">Created At</dt>
                <dd class="col-sm-8">{{ $transaction['created_at'] }}</dd>

                <dt class="col-sm-4">Is Subscription</dt>
                <dd class="col-sm-8">
                    {{ $transaction['is_subscription'] ? 'Yes' : 'No' }}
                </dd>

                @if ($transaction['is_subscription'])
                    <dt class="col-sm-4">Subscription Start</dt>
                    <dd class="col-sm-8">{{ $transaction['subscription_details']['start_date'] ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Subscription End</dt>
                    <dd class="col-sm-8">{{ $transaction['subscription_details']['end_date'] ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Billing Cycle</dt>
                    <dd class="col-sm-8">{{ ucfirst($transaction['subscription_details']['billing_cycle'] ?? 'N/A') }}</dd>
                @endif
            </dl>
        </div>

        @if ($transaction['source'])
            <div class="card mt-5 shadow p-4 rounded-3">
                <h4>Source Details</h4>
                <p><strong>Type:</strong> {{ $transaction['source']['type'] }}</p>
                <p><strong>ID:</strong> {{ $transaction['source']['id'] }}</p>

                <h6 class="mt-3">Details:</h6>
                <pre class="bg-light p-3 rounded border">{{ json_encode($transaction['source']['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                @if ($transaction['source']['nested_source'])
                    <div class="mt-4">
                        <h5>Nested Source</h5>
                        <p><strong>Type:</strong> {{ $transaction['source']['nested_source']['type'] ?? 'N/A' }}</p>
                        <p><strong>ID:</strong> {{ $transaction['source']['nested_source']['id'] ?? 'N/A' }}</p>

                        <h6 class="mt-2">Details:</h6>
                        <pre class="bg-light p-3 rounded border">{{ json_encode($transaction['source']['nested_source']['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')


@endpush
