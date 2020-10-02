@if(isset($banner))
<div class="banner @if($banner->video) video @endif @if($banner->image) image @endif {{$banner->area}} {{$banner->type}} {{$banner->css_class}}" style="{{$banner->styles}}" id="banner_{{$banner->id}}">
    <div class="container">
        @if($banner->image)
        <div class="img">
            <img src="{{ $banner->getImageUrl() }}" />
        </div>
        @endif
        @if($banner->video)
        <div class="video">
            <img src="{{ $banner->video }}" />
        </div>
        @endif
        @if($banner->title)
        <div class="title">
            {!! $banner->title !!}
        </div>
        @endif
         @if($banner->body)
        <div class="title">
            {!! $banner->body !!}
        </div>
        @endif
         @if($banner->button_text)
        <div class="btn banner-btn">
            {!! $banner->button_text !!}
        </div>
        @endif
    </div>
</div>
@endif
