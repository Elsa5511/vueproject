@extends('emails.layout')
@section('content')
<h1>{{ $title ?? '' }}</h1>
<p>Hola {{ $user->name }}</p>
<p>Su código para resetear su contraseña en {{ config('app.name') }} es: <strong class="remarked">{{ $reset->token }}</strong></p>
@endsection
