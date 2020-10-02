<input type="hidden" value="{{ $model->id }}" name="id" />
<div class="form-group row">
    <label for="title" class="col-12 col-md-2 col-form-label">Posicion</label>
    <div class="col-12 col-md-10">
        <input id="title" type="number" style="width: 150px" min="0" class="form-control {{ $errors->has('position') ? ' is-invalid' : '' }}" required name="position" value="{{ old('position', $model->position) }}" autofocus>
        @if ($errors->has('position'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('position') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="title" class="col-12 col-md-2 col-form-label">Destinatario</label>
    <div class="col-12 col-md-10">
        <select class="form-control" required name="send_to" style="width: 250px">
            <option value="">Seleccionar</option>
            <option value="USER" @if(old('send_to', $model->send_to)=='USER')selected @endif>Cliente</option>
            <option value="PROVIDER" @if(old('send_to', $model->send_to)=='PROVIDER')selected @endif>Modelo</option>
            <option value="BOTH" @if(old('send_to', $model->send_to)=='BOTH')selected @endif>Ambos</option>
        </select>
        @if ($errors->has('send_to'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('send_to') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="body" class="col-12 col-md-2 col-form-label">Mensaje</label>
    <div class="col-12 col-md-10">
        <textarea class="form-control {{ $errors->has('message') ? ' is-invalid' : '' }}" required name="message">{{ old('message', $model->message) }}</textarea>
        @if ($errors->has('message'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('message') }}</strong>
        </span>
        @endif
    </div>
</div>



