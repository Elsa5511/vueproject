@extends('admin.layouts.app')
@section('page.title') Detalle de reserva {{ $model->customer->name }} con {{ $model->provider->name }} -  {{ $model->date_from->format('d/m/Y H:i')}} UTC @endsection
@section('content')
<style>
    table th,
    table td{
        vertical-align: middle!important;
    }
</style>
<section class="content">
    <h2>Detalle</h2>
    <table class="table table-striped table-condensed table-bordered">
        <colgroup>
            <col width="200px" />
        </colgroup>
        <tbody>
            <tr>
                <th class="text-right">Estado actual</th>
                <td>
                    <strong>{{ strtoupper($model->status_name) }}</strong>
                </td>
            </tr>
            <tr>
                <th class="text-right">Usuario</th>
                <td><img src="{{ $model->customer->small_avatar_url }}" style="border-radius: 50%;" width="50px" height="50px" />  <a href="{{ route('admin.users.show', ['id'=>$model->customer->id]) }}">{{ $model->customer->name }}</a> - {{ $model->customer->profile->getCompleteName() }}</td>
            </tr>
            <tr>
                <th class="text-right">Modelo</th>
                <td><img src="{{ $model->provider->small_avatar_url }}" style="border-radius: 50%;" width="50px" height="50px" />  <a href="{{ route('admin.providers.show', ['id'=>$model->provider->id]) }}">{{ $model->provider->name }}</a> - {{ $model->provider->profile->getCompleteName() }}</td>
            </tr>
            <tr>
                <th class="text-right">Fecha desde</th>
                <td>{{ $model->date_from->format('d/m/Y H:i') }} UTC</td>
            </tr>
            <tr>
                <th class="text-right">Fecha hasta</th>
                <td>{{ $model->date_to->format('d/m/Y H:i') }} UTC</td>
            </tr>
            <tr>
                <th class="text-right">Cantidad de horas</th>
                <td>{{ $model->hours }}</td>
            </tr>
            <tr>
                <th class="text-right">Precio</th>
                <td>{{$model->currency}} {{ $model->total_price / 100 }}</td>
            </tr>
            <tr>
                <th class="text-right" style="vertical-align: top!important;">Notas</th>
                <td>{{$model->notes }}</td>
            </tr>
            <tr>
                <th class="text-right" style="vertical-align: top!important">Lugar</th>
                <td>            
                    <p>{{$model->address }}</p>
                    <a target="_blank" href="https://www.google.com/maps/search/?api=1&query={{json_decode($model->location)->lat}},{{json_decode($model->location)->lng}}">
                        <img src="https://maps.googleapis.com/maps/api/staticmap?size=300x300&maptype=roadmap&markers=color:red%7C{{json_decode($model->location)->lat}},{{json_decode($model->location)->lng}}&zoom=16&key=AIzaSyCRZkuy1kBFGIvn4dT-Vtx3trK95uw0hTg" />
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
    @if($model->customer_rated || $model->provider_rated)
    <h2>Reviews</h2>
    @if($model->providerRate)
    <p><img src="{{$model->providerRate->author->small_avatar_url }}" style="border-radius: 50%;" width="50px" height="50px" /> {{ $model->providerRate->author->name }} {{$model->providerRate->rate}} "{{$model->providerRate->review}}" </p>
    @endif
    @if($model->customerRate)
    <p><img src="{{$model->customerRate->author->small_avatar_url }}" style="border-radius: 50%;" width="50px" height="50px" /> {{ $model->customerRate->author->name }} Calificacion: <strong>{{$model->customerRate->rate}}</strong> <strong> "{{$model->customerRate->review}}"</strong> </p>
    @endif
    @endif

    <h2>Historial</h2>
    <table class="table table-striped table-condensed table                -bordered">
        <co                lgroup>
            </colgroup>
            <tbody>
                @php
                $history=$model->history()->orderBy('created_at','DESC')->get();
                @endphp
                @foreach($history as $h)
                <tr>
                    <td>
                        <img src="{{$h->user->small_avatar_url }}" style="border-radius: 50%;" width="50px" height="50px" />
                        - {{ $h->user->name}}
                        - {{ $h->status }}
                        - {{ $h->created_at->format('d/m/Y H:i:s') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
    </table>
</section>
@endsection

