<form class="form form-horizontal form-enterkey" method="POST" action="{{ $action }}" autocomplete="off" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    @csrf

    @if($model->profile_status == 'pending')
    <div class="alert alert-warning clearfix alert-dismissible">{!! trans('admin_messages.profile-info-pending', ['link'=> route('admin.account-validations.get-by-user', ['id'=>$model->id])]) !!}</div>
    @endif




    <div class="form-group has-feedback  @if ($errors->has('status')) has-error @endif">
        <div class="col-sm-2"></div>

        <div class="col-sm-10">

            <div class="clearfix my-20px">
                <div class="badge pull-right status status-{{ strtolower($model->status) }}">{{ trans('fields.user-statuses.' . $model->status) }}</div>
            </div>

        </div>
    </div>

    <div class="form-group row">
        <label for="image" class="col-sm-2 control-label">Avatar</label>
        <div class="col-12 col-md-10">
            <figure class="imageFileField display" data-href="{{ $model->getImageUrl() }}">

                <img id="image-01" class="img showFile" src="{{ $model->getImageUrl('250x250') }}" width="250" height="250" />
                <input type="file" class="" name="image" data-width="250" data-height="250" />

            </figure>
            @if ($errors->has('image'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('image') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if ($errors->has('country_id')) has-error @endif">
        <label for="inputCountry" class="col-sm-2 control-label">{{ trans('fields.country') }}</label>
        <div class="col-sm-10">
            @php
            $countries=\App\Models\Country::all();
            @endphp
            <select class="select2 form-control" name="country_id" id="inputCountry">
                @foreach($countries as $country)
                <option value="{{ $country->id }}" @if(old('country_id', $model->country_id)==$country->id) selected @endif>(+{{ $country->phonecode }}) {{ $country->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('country_id'))
            <span class="help-block">
                {{ $errors->first('country_id') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if ($errors->has('phone')) has-error @endif">
        <label for="inputPhone" class="col-sm-2 control-label">{{ trans('fields.phone') }}</label>

        <div class="col-sm-10">
            <input id="inputPhone" type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone', $model->phone) }}" placeholder="{{ __('fields.phone') }}" required autofocus>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            @if ($errors->has('phone'))
            <span class="help-block">
                {{ $errors->first('phone') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if ($errors->has('name')) has-error @endif">
        <label for="inputAlias" class="col-sm-2 control-label">{{ trans('fields.alias') }}</label>

        <div class="col-sm-10">
            <input id="inputAlias" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $model->name) }}" placeholder="{{ __('fields.name') }}" required>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            @if ($errors->has('name'))
            <span class="help-block">
                {{ $errors->first('name') }}
            </span>
            @endif
        </div>
    </div>
    <!--
        <div class="form-group has-feedback  @if ($errors->has('profile[first_name]')) has-error @endif">
            <label for="inputEmail" class="col-sm-2 control-label">{{ trans('fields.first_name') }}</label>
            <div class="col-sm-10">
                <input id="first_name" type="text" class="form-control{{ $errors->has('profile[first_name]') ? ' is-invalid' : '' }}" name="profile[first_name]" value="{{ old('profile[first_name]', $model->profile->first_name) }}" placeholder="{{ __('fields.first_name') }}" >
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                @if ($errors->has('profile[first_name]'))
                <span class="help-block">
                    {{ $errors->first('profile[first_name]') }}
                </span>
                @endif
            </div>
        </div>
    
        <div class="form-group has-feedback  @if ($errors->has('profile[last_name]')) has-error @endif">
            <label for="inputEmail" class="col-sm-2 control-label">{{ trans('fields.last_name') }}</label>
            <div class="col-sm-10">
                <input id="last_name" type="text" class="form-control{{ $errors->has('profile[last_name]') ? ' is-invalid' : '' }}" name="profile[last_name]" value="{{ old('profile[last_name]', $model->profile->last_name) }}" placeholder="{{ __('fields.last_name') }}" >
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                @if ($errors->has('profile[last_name]'))
                <span class="help-block">
                    {{ $errors->first('profile[last_name]') }}
                </span>
                @endif
            </div>
        </div>-->

    <div class="form-group has-feedback  @if ($errors->has('dob')) has-error @endif">
        <label for="inputEmail" class="col-sm-2 control-label">{{ trans('fields.birthdate') }}</label>
        <div class="col-sm-10 size-xl">
            <input id="birthdate" type="text" class="form-control date {{ $errors->has('dob') ? ' is-invalid' : '' }}" name="dob" value="{{ old('dob', $model->dob) }}" placeholder="{{ __('fields.birthdate') }}" >
            <span class="glyphicon glyphicon-calendar form-control-feedback"></span>
            @if ($errors->has('dob'))
            <span class="help-block">
                {{ $errors->first('dob') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if ($errors->has('status')) has-error @endif">
        <label for="inputStatus" class="col-sm-2 control-label">{{ trans('fields.status') }}</label>
        <div class="col-sm-10">           
            <select class="form-control" name="status" id="inputStatus" style="width: 220px">
                <option value="PENDING" @if(old('status', $model->status)==='PENDING') selected @endif>PENDIENTE</option>
                <option value="ACTIVE" @if(old('status', $model->status)==='ACTIVE') selected @endif>ACTIVO</option>
                <option value="INACTIVE" @if(old('status', $model->status)==='INACTIVO') selected @endif>INACTIVO</option>
            </select>
            @if ($errors->has('status'))
            <span class="help-block">
                {{ $errors->first('status') }}
            </span>
            @endif
        </div>
    </div>

    <div class="form-group has-feedback  @if($errors->has('gender')) has-error @endif">
        <label for="inputGender" class="col-sm-2 control-label">{{ trans('fields.gender') }}</label>
        <div class="col-sm-10">           
            <select class="form-control" name="gender" id="inputGender" style="width: 220px">                
                <option value="">Seleccionar</option>
                @foreach(config('custom.genders') as $key=>$value)
                <option @if(old('gender', $model->gender)===$key) selected @endif  value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            @if($errors->has('gender'))
            <span class="help-block">
                {{ $errors->first('gender') }}
            </span>
            @endif
        </div>

    </div>

    <div class="form-group has-feedback  @if($errors->has('gender')) has-error @endif">
        <label for="inputTags" class="col-sm-2 control-label">{{ trans('fields.tags') }}</label>
        <div class="col-sm-10">
            <input id="inputTags" type="text" class="form-control{{ $errors->has('tags') ? ' is-invalid' : '' }}" name="tags" value="{{ old('tags', implode($model->tags, ',')) }}" placeholder="{{ __('fields.tags') }}" required>
           
            @if ($errors->has('tags'))
            <span class="help-block">
                {{ $errors->first('tags') }}
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
            <!--
                        @can('user.delete', $model)
                        <button type='submit' class="btn btn-danger pull-right"><i class='fa fa-trash'></i> {{ trans('generics.trash') }}</button>
                        @endif
            -->
        </div>
    </div>
</form>

@include('admin.components.image-popup', ['title'=>trans('fields.identity_images')])