<form action="" method="POST">
    @csrf
    <input type="hidden" value="SAVE_SETTINGS" name="action" />
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Precio XRP</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>

            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row no-padding no-margin">
                <div class="col-md-7">
                    <p>Api <span class="text-light-blue">USD {{ $stats['xrp_api_price'] }}</span></p>
                    <p class="text-right"><strong>Tipo de cambio</strong> <input type="number" name="settings[conversion_rate]" value="{{ $settings['conversion_rate'] }}" min="0" step="0.01" required class="inline small-input">&nbsp;$&nbsp;&nbsp;</p>
                    <p class="text-right"><strong>Spread compra</strong> <input type="number" name="settings[xrp_buy_spread]" value="{{ $settings['xrp_buy_spread'] }}" min="0" step="0.01" required class="inline small-input"> %</p>
                    <p class="text-right"><strong>Spread venta</strong> <input type="number" name="settings[xrp_sell_spread]" value="{{ $settings['xrp_sell_spread'] }}"  min="0" step="0.01" required class="inline small-input"> %</p>
                </div>
                <div class="col-md-5">
                    <div class="text-center" style="">
                        <strong>Precio de compra</strong>
                    </div>
                    <div class="bg-light-blue clearfix" style="margin-bottom: 8px;line-height: 26px; padding: 0 5px">
                        $ <strong class="pull-right">{{  number_format($stats['xrp_buy_price'],2) }}</strong>
                    </div>

                    <div class="text-center">
                        <strong>Precio de venta</strong>
                    </div>
                    <div class="bg-light-blue clearfix" style="margin-bottom: 5px;line-height: 26px; padding: 0 5px">
                        $ <strong class="pull-right">{{  number_format($stats['xrp_sell_price'] , 2)}}</strong>
                    </div>
                    <div class="col-md-4 no-padding">
                        <p><strong>Delta</strong></p>
                    </div>
                    <div class="col-md-8 no-padding text-light-blue">
                        <p>{{  number_format($stats['xrp_ars_delta'],2) }}</p>
                        <p>{{  number_format($stats['xrp_percent_delta'],2) }}%</p>
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