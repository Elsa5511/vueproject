@extends('admin.layouts.app')
@section('page.title') User: {{$model->id}} - {{ $model->name }}@endsection
@section('content')
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="@if($tab==='basic') active @endif"><a href="{{ route('admin.users.edit', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='basic') true @else false @endif">Datos</a></li>
        <li class="@if($tab==='profile') active @endif"><a href="{{ route('admin.users.profile', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='profile') true @else false @endif">Perfil <span class="status status-{{ strtolower($model->status) }}">{{$model->status}}</span></a></li>
        <li class="@if($tab==='security') active @endif"><a href="{{ route('admin.users.security', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='security') true @else false @endif">Seguridad</a></li>
        <li class="@if($tab==='appointments') active @endif"><a href="{{ route('admin.users.appointments', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='appointments') true @else false @endif">Citas</a></li>

    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_data">
            @include('admin.users.tabs.' . $tab)
        </div>

    </div>
    <!-- /.tab-content -->
</div>


@endsection
@push('scripts')

@endpush