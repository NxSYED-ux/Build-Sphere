<div class="col-md-6 col-xl-4">
    <div class="card border-0 shadow-sm hover-shadow-lg transition-all">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center">
                    <div class="{{ $iconData()['bg'] }} p-3 rounded me-3">
                        <i class="{{ $iconData()['icon'] }} {{ $iconData()['color'] }} fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $transaction['title'] }}</h6>
                        <small class="text-muted">{{ $transaction['created_at'] }}</small>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-4">
                <div>
                    <p class="text-muted small mb-1">Amount</p>
                    <h4 class="mb-0 {{ $iconData()['color'] }}">
                        @if(strtolower($transaction['status']) === 'failed')
                            <span class="text-decoration-line-through">
                        @endif
                                {{ $transaction['price'] }}
                                @if(strtolower($transaction['status']) === 'failed')
                            </span>
                        @endif
                    </h4>
                </div>
                <span class="badge {{ $statusBadge() }}">{{ ucfirst($transaction['status']) }}</span>
            </div>
        </div>
    </div>
</div>
