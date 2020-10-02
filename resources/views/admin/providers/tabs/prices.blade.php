<form class="form form-horizontal form-enterkey" method="POST" action="{{ route('admin.providers.prices-store', ['id'=>$model->id]) }}" autocomplete="off" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    @csrf
    @foreach($prices as $key => $value)
    <div class="form-group has-feedback  @if ($errors->has('hours_' . $key)) has-error @endif">
        <label for="inputHours_{{ $key }}" class="col-sm-2 control-label">{{ $key }} Horas</label>

        <div class="col-sm-10">
            <input id="inputHours_{{$key}}" type="number" class="form-control{{ $errors->has('hours_' . $key) ? ' is-invalid' : '' }}" name="{{ 'hours_' . $key }}" value="{{ old('hours_' . $key , $value) }}" placeholder="Valor" required min="1">

            @if ($errors->has('hours_' . $key))
            <span class="help-block">
                {{ $errors->first('hours_' . $key) }}
            </span>
            @endif
        </div>
    </div>
    @endforeach

    <div class="form-actions">
        <div class="col-sm-10 pull-right">
            <a href='{{ route('admin.providers.index') }}' style="margin-right: 10px;" class="btn btn-default pull-left"><i class='fa fa-reply'></i> {{ trans('generics.cancel') }}</a>
            @can('user.update', $model)
            <button type='submit' class="btn btn-success pull-left btn-delete"><i class='fa fa-save'></i> {{ trans('generics.save') }}</button>
            @endif
            <!--
                        @can('user.delete', $model)
                        <button type='submit' class="btn btn-danger pull-right"><i class='fa fa-trash'></i> {{ trans('generics.trash') }}</button>
                        @endif
            -->
        </div>
    </div>
</form>

