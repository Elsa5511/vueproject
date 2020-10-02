@extends('admin.layouts.app')
@section('page.title') Crear Mensaje @endsection
@section('content')
<section class="content"> 

    <form class="form" action="{{ route('admin.automessages.store', ['id'=>$model->id]) }}" method="post" enctype="multipart/form-data">
        {{ method_field('POST') }}
        @csrf
        @include('admin.automessages.form', ['mode'=>'create', 'model'=>$model]) 
        <div class="form-actions">
            <a href="{{ route('admin.automessages.index') }}" class="btn btn-default pull-left"><i class="fa fa-reply"></i> Cancelar</a>
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar cambios</button>
        </div>
    </form>

</section>
@endsection