<div class="col-lg-3 col-md-6 col-xs-12 padding-y dashborad-card1">
    <div class="shadow text-left rounded-4 pt-3 py-3" style="background-color: {{ $bgColor }} !important;">
        <div class="inner p-2">
            <div class="container">
                <div class="row">
                    <div class="col-8">
                        <h3 id="{{ $valueId }}">{{ $value }}</h3>
                        <p>{{ $title }}</p>
                    </div>
                    <div class="col-4 mt-2">
                        @if($icon)
                            <i class="{{ $icon }}" style="font-size:{{ $iconSize ?? '60px' }};height: 60px; opacity: 0.3; color: white;"></i>
                        @elseif($image)
                            <img src="{{ asset($image) }}" class="" style="width: 70px; height: 65px; margin-right: 30px; opacity: 0.3;" alt="">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                           
