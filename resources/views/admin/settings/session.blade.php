<form action="{{ route('admin.settings.save')}}" class="form-horizontal" method="POST">
    @csrf
    <input type="hidden" value="SAVE_SETTINGS" name="action" />
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Sesión de usuario</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>

            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row no-padding no-margin">
                <div class="form-group">
                    <label for="" class="col-sm-8 control-label">Minutos de duración de la sesión por defecto (solo nuevos usuarios): </label>

                    <div class="col-sm-4">
                        <input type='number' min="1" placeholder="Minutos" class="form-control" name="settings[default_session_lifetime]" required value="{{ $settings['default_session_lifetime'] }}">
                    </div>
                </div>

            </div>
        </div>

        <!-- /.box-body -->
        <div class="box-footer text-right">
            <button type="reset" class="btn btn-default">Recargar</button>
            <button type="submit" class="btn btn-primary">Guardar estos datos</button>
        </div>
    </div>
    <!-- /.box -->
</form>