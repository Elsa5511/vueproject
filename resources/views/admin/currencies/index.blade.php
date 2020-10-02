@extends('admin.layouts.app')
@section('page.title') Monedas @endsection
@section('content')
<p>Por favor ingrese el valor del dolar para cada moneda: </p>
<form class="form form-horizontal form-enterkey" method="POST" action="{{ route('admin.currencies.update') }}" autocomplete="off" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    @csrf
    @foreach($currencies as $k => $c)
    <div class="form-group  @if ($errors->has('phone')) has-error @endif">
        <label for="inputCurrency_[{{$k}}]" class="col-sm-2 control-label">{{ $k }}</label>

        <div class="col-sm-10">
            <input id="inputCurrency_[{{$k}}]" style="width: 150px" type="number" class="form-control{{ $errors->has($k) ? ' is-invalid' : '' }}" name="{{$k}}" value="{{ old($k, $c['value'])!=0?old($k, $c['value'])/100:'' }}" placeholder="Valor" required>

            @if ($errors->has($k))
            <span class="help-block">
                {{ $errors->first($k) }}
            </span>
            @endif
        </div>
    </div>
    @endforeach
    <div class="form-actions">
        <div class="col-sm-10 pull-right">
            <a href='{{ route('admin.home') }}' style="margin-right: 10px;" class="btn btn-default pull-left"><i class='fa fa-reply'></i> {{ trans('generics.cancel') }}</a>
            <button type='submit' class="btn btn-success pull-left btn-delete"><i class='fa fa-save'></i> {{ trans('generics.save') }}</button>
        </div>
    </div>
</form>

@endsection
