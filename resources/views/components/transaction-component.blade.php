{{--@props([--}}
{{--    'source' => 'unknown',--}}
{{--    'source_name' => 'unknown'--}}
{{--])--}}

{{--<div class="card mb-4 shadow-sm">--}}
{{--    <div class="card-header bg-primary text-white">--}}
{{--        <strong>{{ $source_name }}</strong>--}}
{{--        <span class="float-end">ID: {{ $source['id'] ?? 'N/A' }}</span>--}}

{{--        @switch($source_name)--}}
{{--            @case('unit contract')--}}
{{--                <p><strong>Unit:</strong> {{ $source->unit ? $source->unit->name : 'Building not assigned' }}</p>--}}
{{--                @break--}}

{{--            @case('plan')--}}
{{--                <p><strong>Name:</strong> {{ $source ?? 'N/A' }}</p>--}}
{{--                @break--}}

{{--            @default--}}
{{--                @foreach ($source as $key => $value)--}}
{{--                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>--}}
{{--                    @if (is_array($value))--}}
{{--                        <pre class="bg-light p-2 border rounded small">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>--}}
{{--                        @else--}}
{{--                            {{ $value }}--}}
{{--                        @endif--}}
{{--                        </p>--}}
{{--                        @endforeach--}}
{{--                        @endswitch--}}
{{--    </div>--}}
{{--</div>--}}
