@extends('admin.layouts.app')
@section('page.title') Usuarios @endsection
@section('content')
<div class="top-filter clearfix">
    <div class="btn-group pull-right">
        <a href="{{ route('admin.users.index') }}" class="btn btn-default @if(request('status')=='') active @endif">Activos</a>
        <a href="{{ route('admin.users.index') }}?status=inactive" class="btn btn-default @if(request('status')=='inactive') active @endif">Pendientes</a>
        <a href="{{ route('admin.users.index') }}?status=all" class="btn btn-default @if(request('status')=='all') active @endif">Todos</a>
    </div>
</div>




{!! $dataTable->table(['class' => 'table table-bordered table-striped dataTable', 'id' => 'users-table']) !!}


@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

@endpush