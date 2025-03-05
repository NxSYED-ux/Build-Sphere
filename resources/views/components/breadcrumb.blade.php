<section class="content-header header-breadcrumb pt-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mx-3">
            @foreach ($links as $link)
                <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}" aria-current="{{ $loop->last ? 'page' : '' }}">
                        <a href="{{ $link['url'] }}"  style="font-size: 15px;">{{ $link['label'] }}</a>
                </li>
            @endforeach
        </ol>
    </nav>
</section>
