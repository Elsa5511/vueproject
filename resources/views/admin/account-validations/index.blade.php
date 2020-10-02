@extends('admin.layouts.app')
@section('page.title') Validación de cuentas @endsection
@section('content')
<div class="top-filter clearfix">
    <div class="btn-group pull-right">
        <a href="{{ route('admin.account-validations.index') }}" class="btn btn-default @if(request('status')=='') active @endif">{{ trans('filters.pending') }}</a>
        <a href="{{ route('admin.account-validations.index') }}?status=accepted" class="btn btn-default @if(request('status')=='accepted') active @endif">{{ trans('filters.accepted') }}</a>
        <a href="{{ route('admin.account-validations.index') }}?status=rejected" class="btn btn-default @if(request('status')=='rejected') active @endif">{{ trans('filters.rejected') }}</a>
        <a href="{{ route('admin.account-validations.index') }}?status=all" class="btn btn-default @if(request('status')=='all') active @endif">{{ trans('filters.all') }}</a>
    </div>
</div>


{!! $dataTable->table(['class' => 'table table-bordered table-striped dataTable', 'id' => 'requests-table']) !!}
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

@endpush