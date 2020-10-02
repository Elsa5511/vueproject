<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('page.title') | {{ config('app.name', 'Laravel') }}</title>



        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="{{ asset('adm/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('adm/bower_components/font-awesome/css/font-awesome.min.css') }}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset('adm/bower_components/Ionicons/css/ionicons.min.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('adm/css/AdminLTE.min.css') }}">


        <!-- daterange picker
        <link rel="stylesheet" href="{{ asset('adm/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
        <!-- bootstrap datepicker 
        <link rel="stylesheet" href="{{ asset('adm/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">-->
        <link rel="stylesheet" href="{{ asset('adm/bower_components/air-datepicker/dist/css/datepicker.min.css') }}">

        <link rel="stylesheet" href="{{ asset('adm/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

        <!-- swal -->
        <link rel="stylesheet" href="{{ asset('adm/bower_components/sweetalert2/dist/sweetalert2.min.css') }}">

        <!-- summernote -->
        <link rel="stylesheet" href="{{ asset('adm/bower_components/summernote/dist/summernote.css') }}">

        <!-- switchery -->
        <link rel="stylesheet" href="{{ asset('adm/bower_components/switchery/dist/switchery.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adm/bower_components/select2/dist/css/select2.min.css') }}">


        <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
            page. However, you can choose any other skin. Make sure you
            apply the skin class to the body tag so the changes take effect. -->
        <link rel="stylesheet" href="{{ asset('adm/css/skins/_all-skins.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adm/plugins/pace/pace.min.css') }}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->



        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">


        <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/dropzone/basic.css') }}">

        <script>
            var ECHO_HOST = "{{ env('ECHO_HOST', 'app.vaceate.com') }}";
            var USER_TOKEN = "{{ session()->get('user_token') }}";
        </script>

        @stack('styles')

        <!-- Custom style -->
        <link rel="stylesheet" href="{{ asset('adm/css/custom.css')}}">
        @stack('post_styles')


        @yield('header')

    </head>
    <body class="hold-transition skin-red sidebar-mini fixed  internal-page @yield('bodyclass')" id="@yield('bodyid')" @yield('bodyattrs')>
          <div class="wrapper">



            @include('admin.layouts.header')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        @yield('page.title')
                        <small>@yield('page.description')</small>
                    </h1>


                    @section('breadcrumbs')
                    {{ Breadcrumbs::render() }}
                    @stop

                    @yield('breadcrumbs')
                </section>

                <!-- Main content -->
                <section class="content container-fluid">