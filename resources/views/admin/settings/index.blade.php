@extends('admin.layouts.app')
@section('page.title') Settings @endsection
@section('top-content')
<div class="box box-solid">

    <div class="box-body clearfix">
        <p>
            <a href="{{ route('admin.artisan.cache-clear') }}" data-toggle="tooltip" data-placement="left" title="Clear the app cache" class="btn btn-warning pull-right">Clear cache</a>
        </p>
    </div>
    <!-- /.box-body -->
</div>
@endsection
@section('content')
<section class="content no-padding">
@include('admin.settings.extra', ['settings'=>$settings['extra']['values']])


</section>
@endsection