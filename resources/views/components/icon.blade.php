@props([
    'name',
    'type' => 'icon',
    'class' => 'mx-2',
    'size' => '20px'
])

@if($type === 'svg')
    @if($name === 'view')
    @elseif($name === 'add')
        <svg class="{{ $class }}" width="{{ $size }}" height="{{ $size }}" fill="currentColor" viewBox="0 0 448 512"xmlns="http://www.w3.org/2000/svg">
            <path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67
            0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67
            0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/>
        </svg>
    @elseif($name === 'edit')
    @elseif($name === 'search')
    @elseif($name === 'export')
    @endif
@else
    @if($name === 'view')
        <i class="far fa-eye {{ $class }}" style="font-size: {{ $size }}"></i>
    @elseif($name === 'add')
    @elseif($name === 'edit')
        <i class="fas fa-pen {{ $class }}" style="font-size: {{ $size }}"></i>
    @elseif($name === 'search')
        <i class="bx bx-search {{ $class }}" style="font-size: {{ $size }}"></i>
    @elseif($name === 'export')
        <i class="bx bx-export {{ $class }}" style="font-size: {{ $size }}"></i>
    @endif
@endif
