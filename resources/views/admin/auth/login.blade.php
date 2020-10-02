@extends('admin.layouts.external')

@section('page.title'){{ __('pagemeta.login.title') }}@endsection

@section('content')
<div class="login-box">
    <div class="login-logo">
        <b> {{ config('app.name') }} </b>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">{{ trans('pagemeta.login.description') }}</p>
        <form id="sign_in" method="post" autocomplete="off" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group has-feedback  @if ($errors->has('email')) has-error @endif">
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="{{ __('fields.email') }}" required autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if ($errors->has('email'))
                <span class="help-block">
                    {{ $errors->first('email') }}
                </span>
                @endif
            </div>
            <div class="form-group has-feedback @if ($errors->has('password')) has-error @endif">
                <input id="password" type="password" class="form-control " placeholder="{{ __('fields.password') }}" name="password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                <span class="help-block">
                    {{ $errors->first('password') }}
                </span>
                @endif
            </div>
            <div class="row">

                <!-- /.col -->
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('generics.login') }}</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <div class="hr-line"></div>

        <div class="row">
            <div class="col-xs-12">
                <a href="" class="pull-right">{{ trans('generics.forgot-password') }}</a>
            </div>
        </div>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
@endsection
