@props(['source', 'level' => 0])

@php
    $type = $source['type'] ?? 'unknown';
    $details = $source['details'] ?? [];
    $nested = $source['nested_source'] ?? null;
@endphp

<div class="card mb-4 shadow-sm" style="margin-left: {{ $level * 20 }}px;">
    <div class="card-header bg-primary text-white">
        <strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong>
        <span class="float-end">ID: {{ $source['id'] ?? 'N/A' }}</span>
    </div>

    <div class="card-body">

        @switch($type)
            @case('unit contract')
                <p><strong>Unit:</strong> {{ $details['unit_id']->unit ?? 'N/A' }}</p>
                <p><strong>Floor:</strong> {{ $details['floor'] ?? 'N/A' }}</p>
                <p><strong>Size:</strong> {{ $details['size'] ?? 'N/A' }} sq.ft</p>
                <p><strong>Building:</strong> {{ $details['building_name'] ?? 'N/A' }}</p>
                @break

{{--            @case('membership')--}}
{{--                <p><strong>Plan Name:</strong> {{ $details['plan_name'] ?? 'N/A' }}</p>--}}
{{--                <p><strong>Start Date:</strong> {{ $details['start_date'] ?? 'N/A' }}</p>--}}
{{--                <p><strong>End Date:</strong> {{ $details['end_date'] ?? 'N/A' }}</p>--}}
{{--                <p><strong>Status:</strong> {{ ucfirst($details['status'] ?? 'N/A') }}</p>--}}
{{--                @break--}}

            @case('plan')
                <p><strong>Name:</strong> {{ $details['name'] ?? 'N/A' }}</p>
                <p><strong>Description:</strong> {{ $details['description'] ?? 'N/A' }}</p>
                <p><strong>Currency:</strong> {{ $details['currency'] ?? 'N/A' }}</p>
                @break

            @default
                @foreach ($details as $key => $value)
                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                    @if (is_array($value))
                        <pre class="bg-light p-2 border rounded small">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        @else
                            {{ $value }}
                        @endif
                        </p>
                        @endforeach
                        @endswitch

    </div>

    @if ($nested)
        <x-transaction-source :source="$nested" :level="$level + 1" />
    @endif
</div>
