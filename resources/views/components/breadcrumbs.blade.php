@if (count($breadcrumbs)> 1 )

<div class="breadcrumb-wrapper">
    <div class="container">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)

            @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">@if(isset($breadcrumb->icon))<i class="fa fa-{{$breadcrumb->icon}}"></i>&nbsp @endif{!! $breadcrumb->title !!}</a></li>
            @else
            <li class="breadcrumb-item active"> @if(isset($breadcrumb->icon))<i class="fa fa-{{$breadcrumb->icon}}"></i>&nbsp @endif{!! $breadcrumb->title !!}</li>
            @endif

            @endforeach
        </ol>
    </div>
</div>
@endif