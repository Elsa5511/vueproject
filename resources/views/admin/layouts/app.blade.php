@include('admin.layouts.head')
<div class="box">
    @yield('content-header')
    <div class="box-body">

        <div class="row">
            <div class="col-xs-12">
                @yield('top-content')
                @include('admin.components.messages')

                @yield('content')
            </div>
        </div>

    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        @yield('content-footer')
    </div>
    <!-- /.box-footer-->
</div>


@include('admin.layouts.footer')