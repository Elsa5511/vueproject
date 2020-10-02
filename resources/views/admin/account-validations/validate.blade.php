@extends('admin.layouts.app')
@section('page.title') Validar cuenta de: {{$user->id}} - {{ $user->fullName() }}@endsection
@section('content')
@include('admin.account-validations.comparator')
@endsection