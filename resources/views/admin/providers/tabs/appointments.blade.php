<div class="top-filter clearfix">
    <div class="btn-group pull-right">
        <a href="{{ route('admin.providers.appointments', ['id'=>$model->id, 'status'=>'active'])}}" class="btn btn-default @if(request('status')=='' || request('status')=='active') active @endif">Activos</a>
        <a href="{{ route('admin.providers.appointments', ['id'=>$model->id, 'status'=>'pending'])}}" class="btn btn-default @if(request('status')=='pending') active @endif">Pendientes</a>
        <a href="{{ route('admin.providers.appointments', ['id'=>$model->id, 'status'=>'finalized'])}}" class="btn btn-default @if(request('status')=='finalized') active @endif">Finalizados</a>
        <a href="{{ route('admin.providers.appointments', ['id'=>$model->id, 'status'=>'all'])}}" class="btn btn-default @if(request('status')=='all') active @endif">Todos</a>
    </div>
</div>




{!! $dataTable->table(['class' => 'table table-bordered table-striped dataTable', 'id' => 'providers-appointments-table-' . $model->id]) !!}


@push('scripts')
{!! $dataTable->scripts() !!}

@endpush