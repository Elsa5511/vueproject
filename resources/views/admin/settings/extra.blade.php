<form action="{{ route('admin.settings.save')}}" class="form-horizontal" method="POST">
    @csrf
    <input type="hidden" value="SAVE_SETTINGS" name="action" />
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Configuracion del sistema</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>

            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row no-padding no-margin">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Requerir validacion sms: </label>

                    <div class="col-sm-10">
                        <select name="settings[user_require_phone_validation]" class="form-control" style="width: 150px">
                            <option value="0" {{ $settings['user_require_phone_validation']=='0'?'selected': ''}}>No</option>
                            <option value="1" {{ $settings['user_require_phone_validation']=='1'?'selected': ''}}>Si</option>
                        </select>
                    </div>
                </div>
                <!--
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Anticipacion de citas (horas): </label>

                    <div class="col-sm-10">
                        <input name="settings[appointments_min_anticipation]" step="0.1" min="0" class="form-control" style="width: 150px" type="number" value="{{$settings['appointments_min_anticipation']}}" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Intervalo entre citas (horas): </label>

                    <div class="col-sm-10">
                        <input name="settings[appointments_min_interval]" step="0.1" min="0" class="form-control" style="width: 150px" type="number" value="{{$settings['appointments_min_interval']}}" />
                    </div>
                </div>-->
            </div>
        </div>

        <!-- /.box-body -->
        <div class="box-footer text-right">
            <button type="reset" class="btn btn-default">Reload</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
    <!-- /.box -->
</form>