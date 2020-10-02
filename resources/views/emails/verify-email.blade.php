@extends('emails.layout')
@section('content')
<h1>{{ $title ?? '' }}</h1>
<p>Hola {{ $user->name }}</p>
<p>Se ha registrado correctamente en {{ config('app.name') }}!</p>
<p>Para poder ingresar debe validar su cuenta de correo usando este código: <strong class="remarked">{{ $user->email_token }}</strong></p>

@endsection
