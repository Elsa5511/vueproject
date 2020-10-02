<form class="form form-horizontal form-enterkey" method="POST" action="{{ route('admin.users.identity-store', ['id'=>$model->id]) }}" autocomplete="off" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    @csrf    
    @if($verification)
    @php
    $images=$verification->getIdImages();
    @endphp

    <div class="form-group has-feedback  @if($errors->has('status')) has-error @endif">
        <div class="col-sm-2"></div>

        <div class="col-sm-10">

            <div class="clearfix my-20px">
                <div class="badge pull-right status status-{{ strtolower($verification->status) }}">{{ trans('fields.verification-statuses.' . $verification->status) }}</div>
            </div>

        </div>
    </div>


    <div class="form-group">
        <label for="image" class="col-sm-2 control-label">Numero de documento: </label>
        <div class="        col-sm-10">
            <input class="form-control col-12" type="text"  value="{{ $verification->identity_id }}" disabled="" />
        </div>
    </div>

    <div class="form-group">
        <label for="image" class="col-sm-2 control-label">Pais: </label>
        <div class="col-sm-10">
            @php
            $countries=\App\Models\Country::all();
            @endphp
            <select class="select2 form-control" name="country_id" id="inputCountry">
                @foreach($countries as $country)
                <option value="{{ $country->id }}" @if(old('country_id', $verification->country_id)==$country->id) selected @endif> {{ $country->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('country_id'))
            <span class="help-block">
                {{ $errors->first('country_id') }}
            </span>
            @endif
        </div>
    </div>



    <div class="form-group has-feedback">
        <label for="image" class="col-sm-2 control-label">Fotos: </label>
        <div class="col-sm-10">
            @if(count($images))
            <a href="{{  $verification->getIdImageUrl($images['front']) }}" target="_blank"><img width="200" height="200" class="image" style="border:3px solid #ddd; margin-right: 10px"  src="{{ $verification->getIdImageUrl($images['front'], '200x200') }}" /></a>
            <a href="{{ $verification->getIdImageUrl($images['back']) }}" target="_blank"><img width="200" height="200" class="image" style="border:3px solid #ddd" src="{{ $verification->getIdImageUrl($images['back'], '200x200') }}" /></a>
            @endif
        </div>
    </div>


    <div class="form-actions">
        <div class="col-sm-10 pull-right">
            <a href='{{ route('admin.users.index') }}' style="margin-right: 10px;" class="btn btn-default pull-left"><i class='fa fa-reply'></i> {{ trans('generics.cancel') }}</a>
            @can('user.update', $model)
            <a href="{{ route('admin.users.identity', ['id'=>$model->id]) . '?action=approve' }}" class="btn btn-success pull-left"><i class='fa fa-check'></i> ACEPTAR</a>
            <a href="{{ route('admin.users.identity', ['id'=>$model->id]) . '?action=reject' }}"  class="btn btn-danger pull-right"><i class='fa fa-times'></i> RECHAZAR</a>
            @endif

        </div>
    </div>
    @else
    <p class="alert alert-warning">Este usuario aún no ha solicitado verificación de identidad.</p>
    @endif

</form>
