@extends('admin.layouts.app')
@section('page.title') Users @endsection
@section('content')
<div class="top-filter clearfix">
    <div class="btn-group pull-right">
        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-default @if(request('status')=='') active @endif">Pending</a>
        <a href="{{ route('admin.withdrawals.index') }}?status=IN_PROCESS" class="btn btn-default @if(request('status')=='IN_PROCESS') active @endif">In process</a>
        <a href="{{ route('admin.withdrawals.index') }}?status=FINISHED" class="btn btn-default @if(request('status')=='FINISHED') active @endif">Finished</a>
        <a href="{{ route('admin.withdrawals.index') }}?status=CANCELLED" class="btn btn-default @if(request('status')=='CANCELLED') active @endif">Cancelled</a>
        <a href="{{ route('admin.withdrawals.index') }}?status=all" class="btn btn-default @if(request('status')=='all') active @endif">All</a>
    </div>
</div>


{!! $dataTable->table(['class' => 'table table-bordered table-striped dataTable', 'id' => 'users-table']) !!}
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

@endpush