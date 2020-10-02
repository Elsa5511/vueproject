@extends('admin.layouts.app')
@section('page.title') User: {{$model->id}} - {{ $model->name }}@endsection
@section('content')
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="@if($tab==='basic') active @endif"><a href="{{ route('admin.providers.edit', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='basic') true @else false @endif">Datos</a></li>
        <li class="@if($tab==='profile') active @endif"><a href="{{ route('admin.providers.profile', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='profile') true @else false @endif">Perfil <span class="status status-{{ strtolower($model->status) }}">{{$model->status}}</span></a></li>
        <li class="@if($tab==='identity') active @endif"><a href="{{ route('admin.providers.identity', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='identity') true @else false @endif">Identidad <span class="status status-{{  (($model->identity_verified)? 'active':'pending') }}">{{(($model->identity_verified)? 'Verificada':'Pendiente')}}</span></a></li>
        <li class="@if($tab==='security') active @endif"><a href="{{ route('admin.providers.security', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='security') true @else false @endif">Seguridad</a></li>
        <li class="@if($tab==='prices') active @endif"><a href="{{ route('admin.providers.prices', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='prices') true @else false @endif">Precios</a></li>
        <li class="@if($tab==='gallery') active @endif"><a href="{{ route('admin.providers.gallery', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='gallery') true @else false @endif">Galería</a></li>
        <li class="@if($tab==='appointments') active @endif"><a href="{{ route('admin.providers.appointments', ['id'=>$model->id]) }}" aria-expanded="@if($tab==='appointments') true @else false @endif">Citas</a></li>

    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_data">
            @include('admin.providers.tabs.' . $tab)
        </div>

    </div>
    <!-- /.tab-content -->
</div>


@endsection
@push('scripts')

@endpush