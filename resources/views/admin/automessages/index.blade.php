@extends('admin.layouts.app')
@section('page.title') Mensajes automaticos @endsection
@section('content')
<div class="top-filter clearfix">
    <a href="{{ route('admin.automessages.create') }}" class="btn btn-success pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Crear nuevo</a>
</div>


{!! $dataTable->table(['class' => 'table table-bordered table-striped dataTable', 'id' => 'automessages-table']) !!}
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

@endpush
