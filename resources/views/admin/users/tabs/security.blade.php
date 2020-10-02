<form class="form form-horizontal form-enterkey" method="POST" action="{{ route('admin.users.security-store', ['id'=>$model->id]) }}" autocomplete="off" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    @csrf

    <div class="form-group has-feedback  @if ($errors->has('password')) has-error @endif">
        <label for="inputPassword" class="col-sm-2 control-label">Nuevo password</label>

        <div class="col-sm-10">
            <input id="inputPassword" type="password" minlength="6" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ old('password')}}" placeholder="{{ __('fields.password') }}" required autofocus>
            @if ($errors->has('password'))
            <span class="help-block">
                {{ $errors->first('password') }}
            </span>
            @endif
        </div>
    </div>
    
    <div class="form-group has-feedback  @if ($errors->has('password')) has-error @endif">
        <label for="inputPhoneVerified" class="col-sm-2 control-label">Telefono verificado</label>

        <div class="col-sm-10">
            <input id="inputPassword" type="checkbox" class="" name="phone_verified" value="1" @if(old('phone_verified', $model->phone_verified)) checked @endif>
           
        </div>
    </div>

    <div class="form-actions">
        <div class="col-sm-10 pull-right">
            <a href='{{ route('admin.users.index') }}' style="margin-right: 10px;" class="btn btn-default pull-left"><i class='fa fa-reply'></i> {{ trans('generics.cancel') }}</a>
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

