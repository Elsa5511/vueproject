<form action="" method="POST">
    @csrf
    <input type="hidden" value="SAVE_SETTINGS" name="action" />
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Comisión e Impuestos</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">

                <div class="col-sm-4">
                    <p class="text-right"><strong>Débitos y créditos</strong> <input type="number" name="settings[debits_credits_tax]" value="{{ $settings['debits_credits_tax'] }}" min="0" step="0.01" required class="inline small-input"> %</p>
                    <p class="text-right"><strong>Comisión depósitos</strong> <input type="number" name="settings[deposit_commission]" value="{{ $settings['deposit_commission'] }}" min="0" step="0.01" required class="inline small-input"> %</p>
                    <p class="text-right"><strong>Comisión extracción</strong> <input type="number" name="settings[withdraw_commission]" value="{{ $settings['withdraw_commission'] }}"  min="0" step="0.01" required class="inline small-input"> %</p>
                </div>
                <div class="col-sm-4">
                    <p class="text-right"><strong>Venta BTC</strong> <input type="number" name="settings[btc_sell_commission]" value="{{ $settings['btc_sell_commission'] }}" min="0" step="0.01" required class="inline small-input"> %</p>
                    <p class="text-right"><strong>Compra BTC</strong> <input type="number" name="settings[btc_buy_commission]" value="{{ $settings['btc_buy_commission'] }}" min="0" step="0.01" required class="inline small-input"> %</p>
                </div>
                <div class="col-sm-4">
                    <p class="text-right"><strong>Venta ETH</strong> <input type="number" name="settings[xrp_sell_commission]" value="{{ $settings['xrp_sell_commission'] }}"  min="0" step="0.01" required class="inline small-input"> %</p>
                    <p class="text-right"><strong>Compra ETH</strong> <input type="number" name="settings[xrp_buy_commission]" value="{{ $settings['xrp_buy_commission'] }}"  min="0" step="0.01" required class="inline small-input"> %</p>

                </div>



            </div>
            <!-- /.row -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer text-right">
            <button type="reset" class="btn btn-default">Recargar</button>
            <button type="submit" class="btn btn-primary">Guardar estos datos</button>
        </div>
    </div>
    <!-- /.box -->
</form>