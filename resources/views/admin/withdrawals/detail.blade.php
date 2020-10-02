@extends('admin.layouts.app')
@section('page.title') Withdrawal Request: {{$model->method}} - {{ $model->user->name }}@endsection
@section('content')
@include('admin.withdrawals.form', ['action'=>route('admin.withdrawals.update',['id'=>$model->id])])

@endsection
@push('scripts')

@endpush