<form class="form form-horizontal form-enterkey" method="POST" action="{{ $action }}" autocomplete="off" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    @csrf

    @if($model->profile_status == 'pending')
    <div class="alert alert-warning clearfix alert-dismissible">{!! trans('admin_messages.profile-info-pending', ['link'=> route('admin.account-validations.get-by-user', ['id'=>$model->id])]) !!}</div>
    @endif




    <div class="form-group has-feedback  @if($errors->has('status')) has-error @endif">
        <div class="col-sm-2"></div>

        <div class="col-sm-10">

            <div class="clearfix my-20px">
                <div class="badge pull-right status status-{{ strtolower($model->status) }}">{{ trans('fields.user-statuses.' . $model->status) }}</div>
            </div>

        </div>
    </div>


    <div class="form-group has-feedback  @if($errors->has('profile[first_name]')) has-error @endif">
        <label for="inputEmail" class="col-sm-2 control-label">{{ trans('fields.first_name') }}</label>
        <div class="col-sm-10">
            <input id="first_name" type="text" class="form-control{{ $errors->has('profile[first_name]') ? ' is-invalid' : '' }}" name="profile[first_name]" value="{{ old('profile[first_name]', $model->profile->first_name) }}" placeholder="{{ __('fields.first_name') }}" >
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            @if($errors->has('profile[first_name]'))
            <span class="help-block">
                {{ $errors->first('profile[first_name]') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if($errors->has('profile[last_name]')) has-error @endif">
        <label for="inputEmail" class="col-sm-2 control-label">{{ trans('fields.last_name') }}</label>
        <div class="col-sm-10">
            <input id="last_name" type="text" class="form-control{{ $errors->has('profile[last_name]') ? ' is-invalid' : '' }}" name="profile[last_name]" value="{{ old('profile[last_name]', $model->profile->last_name) }}" placeholder="{{ __('fields.last_name') }}" >
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            @if($errors->has('profile[last_name]'))
            <span class="help-block">
                {{ $errors->first('profile[last_name]') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if ($errors->has('profile[country_id]')) has-error @endif">
        <label for="inputCountry" class="col-sm-2 control-label">Nacionalidad:</label>
        <div class="col-sm-10">
            @php
            $countries=\App\Models\Country::all();
            @endphp
            <select class="select2 form-control" name="profile[country_id]" id="inputCountry">
                <option value="">Seleccionar</option>
                @foreach($countries as $country)
                <option value="{{ $country->id }}" @if(old('profile[country_id]', $model->profile->country_id)==$country->id) selected @endif>{{ $country->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('profile[country_id]'))
            <span class="help-block">
                {{ $errors->first('profile[country_id]') }}
            </span>
            @endif
        </div>
    </div>


    <div class="form-group has-feedback  @if($errors->has('profile[hair_color]')) has-error @endif">
        <label for="inputHair" class="col-sm-2 control-label">{{ trans('fields.hair_color') }}</label>
        <div class="col-sm-10">           
            <select class="form-control" name="profile[hair_color]" id="inputHair" style="width: 220px">                
                <option value="">Seleccionar</option>
                @foreach(config('custom.hair_colors') as $key=>$value)
                <option @if(old('profile[hair_color]', $model->profile->hair_color)===$key) selected @endif  value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            @if($errors->has('profile[hair_color]'))
            <span class="help-block">
                {{ $errors->first('profile[hair_color]') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if($errors->has('profile[eyes_color]')) has-error @endif">
        <label for="inputEyes" class="col-sm-2 control-label">{{ trans('fields.eyes_color') }}</label>
        <div class="col-sm-10">           
            <select class="form-control" name="profile[eyes_color]" id="inputEyes" style="width: 220px">                
                <option value="">Seleccionar</option>
                @foreach(config('custom.eyes_colors') as $key=>$value)
                <option @if(old('profile[eyes_color]', $model->profile->eyes_color)===$key) selected @endif  value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            @if($errors->has('profile[eyes_color]'))
            <span class="help-block">
                {{ $errors->first('profile[eyes_color]') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if($errors->has('profile[skin_color]')) has-error @endif">
        <label for="inputSkin" class="col-sm-2 control-label">{{ trans('fields.skin_color') }}</label>
        <div class="col-sm-10">           
            <select class="form-control" name="profile[skin_color]" id="inputSkin" style="width: 220px">                
                <option value="">Seleccionar</option>
                @foreach(config('custom.skin_colors') as $key=>$value)
                <option @if(old('profile[skin_color]', $model->profile->skin_color)===$key) selected @endif  value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            @if($errors->has('profile[skin_color]'))
            <span class="help-block">
                {{ $errors->first('profile[skin_color]') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if($errors->has('profile[corporal_complexion]')) has-error @endif">
        <label for="inputCorporalComplexion" class="col-sm-2 control-label">{{ trans('fields.corporal_complexion') }}</label>
        <div class="col-sm-10">           
            <select class="form-control" name="profile[corporal_complexion]" id="inputCorporalComplexion" style="width: 220px">                
                <option value="">Seleccionar</option>
                @foreach(config('custom.corporal_complexions') as $key=>$value)
                <option @if(old('profile[corporal_complexion]', $model->profile->corporal_complexion)===$key) selected @endif  value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            @if($errors->has('profile[corporal_complexion]'))
            <span class="help-block">
                {{ $errors->first('profile[corporal_complexion]') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if($errors->has('profile[sexual_orientation]')) has-error @endif">
        <label for="inputSexualOrientation" class="col-sm-2 control-label">{{ trans('fields.sexual_orientation') }}</label>
        <div class="col-sm-10">           
            <select class="form-control" name="profile[sexual_orientation]" id="inputSexualOrientation" style="width: 220px">                
                <option value="">Seleccionar</option>
                @foreach(config('custom.sexual_orientations') as $key=>$value)
                <option @if(old('profile[sexual_orientation]', $model->profile->sexual_orientation)===$key) selected @endif  value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            @if($errors->has('profile[sexual_orientation]'))
            <span class="help-block">
                {{ $errors->first('profile[sexual_orientation]') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-actions">
        <div class="col-sm-10 pull-right">
            <a href='{{ route('admin.providers.index') }}' style="margin-right: 10px;" class="btn btn-default pull-left"><i class='fa fa-reply'></i> {{ trans('generics.cancel') }}</a>
            @can('user.update', $model)
            <button type='submit' class="btn btn-success pull-left btn-delete"><i class='fa fa-save'></i> {{ trans('generics.save') }}</button>
            @endif

        </div>
    </div>
</form>
