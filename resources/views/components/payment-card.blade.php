@props([
    'cardNumber' => '•••• •••• •••• ••••',
    'cardHolder' => '',
    'expiry' => '••/••',
    'cvv' => '•••',
    'cardType' => null,
    'isPrimary' => false,
    'showActions' => true
])

@php
    $cardStyles = [
        'visa' => [
            'background' => 'linear-gradient(135deg, #1a1f71 0%, #0065a3 100%)',
            'icon' => 'bxl-visa',
            'accent' => 'linear-gradient(to right, #f9a61a, #f8d568)',
            'textColor' => '#1a1f71'
        ],
        'mastercard' => [
            'background' => 'linear-gradient(135deg, #EB001B 0%, #F79E1B 100%)',
            'icon' => 'bxl-mastercard',
            'accent' => 'rgba(255,255,255,0.9)',
            'textColor' => '#EB001B'
        ],
        'amex' => [
            'background' => 'linear-gradient(135deg, #016FD0 0%, #00A3E0 100%)',
            'icon' => 'fa-cc-amex',
            'accent' => 'rgba(255,255,255,0.9)',
            'textColor' => '#016FD0'
        ],
        'discover' => [
            'background' => 'linear-gradient(135deg, #FF6000 0%, #FFA000 100%)',
            'icon' => 'fa-cc-discover',
            'accent' => 'rgba(255,255,255,0.9)',
            'textColor' => '#FF6000'
        ],
        'diners' => [
            'background' => 'linear-gradient(135deg, #0079BE 0%, #00A3E0 100%)',
            'icon' => 'fa-cc-diners-club',
            'accent' => 'rgba(255,255,255,0.9)',
            'textColor' => '#0079BE'
        ],
        'unionpay' => [
            'background' => 'linear-gradient(135deg, #04517A 0%, #0588C9 100%)',
            'icon' => 'bx-credit-card',
            'accent' => 'rgba(255,255,255,0.9)',
            'textColor' => '#04517A'
        ],
        'default' => [
            'background' => 'linear-gradient(135deg, #4a5568 0%, #0E131DFF 100%)',
            'icon' => 'bx-credit-card',
            'accent' => 'rgba(255,255,255,0.9)',
            'textColor' => '#4a5568'
        ]
    ];

    $normalizedCardType = strtolower($cardType);
    $style = $cardStyles[$normalizedCardType] ?? $cardStyles['default'];
    $iconClass = $style['icon'];
    $isFontAwesome = str_contains($iconClass, 'fa-');

    // Format card number to show only last 4 digits
    $formattedCardNumber = $cardNumber;
    if (strlen($cardNumber) >= 4 && $cardNumber !== '•••• •••• •••• ••••') {
        $lastFour = substr($cardNumber, -4);
        $formattedCardNumber = '•••• •••• •••• ' . $lastFour;
    }
@endphp

<div class="col-md-6 mb-3">
    <div class="payment-card p-3" style="background: {{ $style['background'] }}; border-radius: 10px; color: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); position: relative; overflow: hidden;">

        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>

        <div class="d-flex justify-content-between align-items-center mb-1" style="position: relative; z-index: 2;">
            @if($isFontAwesome)
                <i class="fab {{ $iconClass }}" style="font-size: 40px; color: white;"></i>
            @else
                <i class='bx {{ $iconClass }}' style="font-size: 40px; color: white;"></i>
            @endif

            @if($isPrimary)
                <span class="badge" style="background: {{ $style['accent'] }}; color: {{ $style['textColor'] }}; font-weight: bold;">
                    Primary
                </span>
            @endif
        </div>

        @if($cardHolder)
            <div class="mb-2" style="position: relative; z-index: 2;">
                <span class="text-white-50" style="font-size: 0.8rem;">Card Holder</span>
                <h6 class="mb-0">{{ $cardHolder }}</h6>
            </div>
        @endif

        <div class="mb-1" style="position: relative; z-index: 2;">
            <span class="text-white-50" style="font-size: 0.8rem;">Card Number</span>
            <h5 class="mb-0" style="letter-spacing: 1px;">{{ $formattedCardNumber }}</h5>
        </div>

        <div class="row" style="position: relative; z-index: 2;">
            <div class="col-6">
                <span class="text-white-50" style="font-size: 0.8rem;">Expires</span>
                <h6 class="mb-0">{{ $expiry }}</h6>
            </div>
            <div class="col-6">
                <span class="text-white-50" style="font-size: 0.8rem;">CVV</span>
                <h6 class="mb-0">{{ $cvv }}</h6>
            </div>
        </div>

        @if($showActions)
            <div class="d-flex justify-content-end gap-2 mt-1" style="position: relative; z-index: 2;">
                <button class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: none;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endif
    </div>
</div>
