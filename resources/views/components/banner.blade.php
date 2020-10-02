@if(isset($banner))
    @if(view()->exists('components.banner-' . $banner->area))
        @include('components.banner-' . $banner->area, ['banner'=>$banner])
    @else
        @include('components.banner-default', ['banner'=>$banner])
    @endif
@endif
