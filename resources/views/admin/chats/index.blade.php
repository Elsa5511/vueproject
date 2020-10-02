@extends('admin.layouts.app')
@section('page.title') Chats @endsection
@section('content')
<div class="top-filter clearfix">
    <div class="btn-group pull-right">
        <a href="{{ route('admin.providers.index') }}" class="btn btn-default @if(request('status')=='' || request('status')=='active') active @endif">Activos</a>
        <a href="{{ route('admin.providers.index') }}?status=pending" class="btn btn-default @if(request('status')=='pending') active @endif">Pendientes</a>
        <a href="{{ route('admin.providers.index') }}?status=all" class="btn btn-default @if(request('status')=='all') active @endif">Todos</a>
    </div>
</div>




{!! $dataTable->table(['class' => 'table table-bordered table-striped dataTable', 'id' => 'chats-table']) !!}


@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

@endpush