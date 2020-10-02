<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Pendientes</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>

        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <h4 class="box-title">Validación</h4>
        <div class="row">
            <div class="col-xs-6">Usuarios</div>
            <div class="col-xs-6">
                @if(isset($stats['pendingUsers']) && $stats['pendingUsers'])
                <a href="{{ route('admin.account-validations.index') }}">
                    @endif
                    <strong class="badge bg-{{ isset($stats['pendingUsers']) && $stats['pendingUsers'] ? 'yellow':'blue' }}">{{ $stats['pendingUsers'] or '0'}}</strong> 
                    @if(isset($stats['pendingUsers']) && $stats['pendingUsers'])
                </a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">CBUs</div>
            <div class="col-xs-6">
                @if(isset($stats['pendingCbus']) && $stats['pendingCbus'])
                <a href="{{ route('admin.cbus.index') }}">
                    @endif
                    <strong class="badge bg-{{ isset($stats['pendingCbus']) && $stats['pendingCbus'] ? 'yellow':'blue' }}">{{ $stats['pendingCbus'] or '0'}}</strong> 
                    @if(isset($stats['pendingCbus']) && $stats['pendingCbus'])
                </a>
                @endif
            </div>
        </div>
        <h4 class="box-title">Operación</h4>
        <div class="row">
            <div class="col-xs-6">Fondos</div>
            <div class="col-xs-6">
                @if(isset($stats['pendingFunds']) && $stats['pendingFunds'])
                <a href="">
                    @endif
                    <strong class="badge bg-{{ isset($stats['pendingFunds']) && $stats['pendingFunds'] ? 'yellow':'blue' }}">{{ $stats['pendingFunds'] or '0'}}</strong> 
                    @if(isset($stats['pendingFunds']) && $stats['pendingFunds'])
                </a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">Compraventa</div>
            <div class="col-xs-6">
                @if(isset($stats['pendingSellOrBuy']) && $stats['pendingSellOrBuy'])
                <a href="">
                    @endif
                    <strong class="badge bg-{{ isset($stats['pendingSellOrBuy']) && $stats['pendingSellOrBuy'] ? 'yellow':'blue' }}">{{ $stats['pendingSellOrBuy'] or '0'}}</strong> 
                    @if(isset($stats['pendingSellOrBuy']) && $stats['pendingSellOrBuy'])
                </a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">Transacción</div>
            <div class="col-xs-6">
                @if(isset($stats['pendingTransactions']) && $stats['pendingTransactions'])
                <a href="">
                    @endif
                    <strong class="badge bg-{{ isset($stats['pendingTransactions']) && $stats['pendingTransactions'] ? 'yellow':'blue' }}">{{ $stats['pendingTransactions'] or '0'}}</strong> 
                    @if(isset($stats['pendingTransactions']) && $stats['pendingTransactions'])
                </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer text-center">

    </div>
    <!-- /.box-footer -->
</div>