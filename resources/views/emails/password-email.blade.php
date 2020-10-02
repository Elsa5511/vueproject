@extends('emails.layout')
@section('content')
<h1>{{ $title ?? '' }}</h1>
<p>Hola {{ $user->name }}</p>
<p>Su nueva contraseña en {{ config('app.name') }} es: <strong class="remarked">{{ $password }}</strong></p>
@endsection
