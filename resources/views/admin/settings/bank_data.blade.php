<form action="{{ route('admin.settings.save')}}" class="form-horizontal" method="POST">
    @csrf
    <input type="hidden" value="SAVE_SETTINGS" name="action" />
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Datos bancarios</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>

            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row no-padding no-margin">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Destinatario: </label>

                    <div class="col-sm-10">
                        <input type='text' placeholder="Titular de la cuenta" class="form-control" name="settings[bank_data_account_name]" required value="{{ $settings['bank_data_account_name'] }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">CUIT: </label>

                    <div class="col-sm-10">
                        <input type='text' placeholder="CUIT de la cuenta" class="form-control" name="settings[bank_data_account_cuit]" required value="{{ $settings['bank_data_account_cuit'] }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Banco: </label>

                    <div class="col-sm-10">
                        <input type='text' placeholder="Nombre del banco" class="form-control" name="settings[bank_data_bank_name]" required value="{{ $settings['bank_data_bank_name'] }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">CBU: </label>

                    <div class="col-sm-10">
                        <input type='text' placeholder="CBU de la cuenta" class="form-control" name="settings[bank_data_cbu]" required value="{{ $settings['bank_data_cbu'] }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">N° de cuenta: </label>

                    <div class="col-sm-10">
                        <input type='text' placeholder="Número de la cuenta" class="form-control" name="settings[bank_data_account_number]" required value="{{ $settings['bank_data_account_number'] }}">
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