@props(['transaction', 'routeName'])

@php
    $status = strtolower($transaction['status'] ?? '');
    $type = strtolower($transaction['type'] ?? '');

    // Determine icon classes
    $iconClass = match(true) {
        $status === 'failed' => 'fas fa-ban text-danger',
        $type === 'credit' => 'fas fa-download text-success',
        default => 'fas fa-upload text-primary'
    };

    // Determine status icon
    $statusIcon = match($status) {
        'completed' => 'fas fa-check-circle',
        'pending' => 'fas fa-clock',
        'failed' => 'fas fa-exclamation-circle',
        default => 'fas fa-info-circle'
    };

    // Determine background class
    $bgClass = match(true) {
        $status === 'failed' => 'bg-danger bg-opacity-10',
        $type === 'credit' => 'bg-success bg-opacity-10',
        $status === 'pending' => 'bg-warning bg-opacity-10',
        default => 'bg-primary bg-opacity-10'
    };

    // Determine amount class
    $amountClass = match(true) {
        $status === 'failed' => 'amount-failed',
        $type === 'credit' => 'amount-positive',
        $status === 'pending' => 'amount-pending',
        default => 'amount-negative'
    };

    // Determine status badge class
    $statusBadgeClass = match($status) {
        'completed' => 'badge-completed',
        'pending' => 'badge-pending',
        'failed' => 'badge-failed',
        default => 'badge-secondary'
    };
@endphp

<div class="col-md-6 col-xl-4 py-2 transaction-card-container" data-transaction-id="{{ $transaction['id'] ?? '' }}">
    <a href="{{ route($routeName, $transaction['id']) }}" class="text-white text-decoration-none" style="color: #fff !important;">

        <div class="card border-0 shadow hover-shadow-lg transition-all h-100 transaction-card">
        <div class="card-body p-4 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-3 flex-grow-1">
                <div class="d-flex align-items-center">
                    <div class="icon-container {{ $bgClass }} rounded-circle me-3">
                        <i class="{{ $iconClass }} fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $transaction['title'] ?? 'Untitled Transaction' }}</h6>
                        <small class="small">{{ $transaction['created_at'] ?? '' }}</small>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-auto">
                <div>
                    <p class="small mb-1">Amount</p>
                    <h4 class="mb-0 {{ $amountClass }}">
                        @if($status === 'failed')
                            <span class="amount-failed">
                        @endif
                                {{ $type === 'credit' ? '+' : '-' }}{{ $transaction['price'] ?? '0.00' }}
                                @if($status === 'failed')
                            </span>
                        @endif
                    </h4>
                </div>
                <span class="badge {{ $statusBadgeClass }} px-3 py-2">
                    <i class="{{ $statusIcon }} me-1"></i>
                    {{ ucfirst($status) }}
                </span>
            </div>
        </div>
    </div>
    </a>
</div>

<style>
    .transaction-card {
        background-color: var(--body-background-color) !important;
    }
    .transaction-card .icon-container {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .transaction-card .amount-positive {
        color: #28a745;
    }
    .transaction-card .amount-negative {
        color: var(--color-blue);
    }
    .transaction-card .amount-pending {
        color: #ffc107;
    }
    .transaction-card .amount-failed {
        color: #dc3545;
        text-decoration: line-through;
    }
    .transaction-card .badge-completed {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .transaction-card .badge-pending {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    .transaction-card .badge-failed {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
</style>
