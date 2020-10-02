


</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

@include('admin.layouts.controlsidebar')

<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        V. {{ config('app.version', '1.0.0') }}.
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; {{ date('Y') }} - {{ config('app.name', 'Laravel') }}.
</footer>

</div>
<!-- ./wrapper -->


@stack('prescripts')

<!-- REQUIRED JS SCRIPTS -->

<script>
  var module = { };
</script>
<script src="https://cdn.jsdelivr.net/npm/socket.io-client@2.2.0/dist/socket.io.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo"></script>

<!-- jQuery 3 -->
<script src="{{ asset('adm/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('adm/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>


@if (config('app.env')!='local') 
<!-- PACE -->
<script src="{{ asset('adm/bower_components/PACE/pace.min.js') }}"></script>
@endif



<!-- SlimScroll -->
<script src="{{ asset('adm/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('adm/bower_components/fastclick/lib/fastclick.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('adm/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adm/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('adm/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adm/bower_components/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ url('/vendor/datatables/buttons.server-side.js') }}"></script>
<script src="{{ url('/vendor/dropzone/dropzone.js') }}"></script>

<!-- date-range-picker
<script src="{{ asset('adm/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('adm/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- bootstrap datepicker 
<script src="{{ asset('adm/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('adm/bower_components/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js') }}"></script>
-->

<script src="{{ asset('adm/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

<script src="{{ asset('adm/bower_components/air-datepicker/dist/js/datepicker.min.js') }}"></script>
<script src="{{ asset('adm/bower_components/air-datepicker/dist/js/i18n/datepicker.es.js') }}"></script>
<!-- swal -->
<script src="{{ asset('adm/bower_components/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('adm/bower_components/summernote/dist/summernote.js') }}"></script>

<script src="{{ asset('adm/bower_components/switchery/dist/switchery.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('adm/js/adminlte.min.js') }}"></script>
<script src="{{ asset('adm/js/imageupload.js') }}"></script>
<script src="{{ asset('adm/js/custom.js') }}"></script>



@stack('scripts')
</body>
</html>