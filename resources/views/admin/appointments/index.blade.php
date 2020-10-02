@extends('admin.layouts.app')
@section('page.title') Reservas @endsection
@section('content')
<div class="top-filter clearfix">
    <div class="btn-group pull-right">
        <a href="{{ route('admin.appointments', ['status'=>'active'])}}" class="btn btn-default @if(request('status')=='' || request('status')=='active') active @endif">Activos</a>
        <a href="{{ route('admin.appointments', ['status'=>'pending'])}}" class="btn btn-default @if(request('status')=='pending') active @endif">Pendientes</a>
        <a href="{{ route('admin.appointments', ['status'=>'finalized'])}}" class="btn btn-default @if(request('status')=='finalized') active @endif">Finalizados</a>
        <a href="{{ route('admin.appointments', ['status'=>'all'])}}" class="btn btn-default @if(request('status')=='all') active @endif">Todos</a>
    </div>
</div>




{!! $dataTable->table(['class' => 'table table-bordered table-striped dataTable', 'id' => 'appointments-table']) !!}


@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

@endpush