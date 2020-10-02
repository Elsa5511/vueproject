@extends('admin.layouts.app')
@section('page.title') Editar Pagina "{{ $model->title }}" @endsection
@section('content')
<section class="content"> 

    <form class="form" action="{{ route('admin.pages.update', ['id'=>$model->id]) }}" method="post" enctype="multipart/form-data">
        {{ method_field('PUT') }}
        @csrf
        @include('admin.pages.form', ['mode'=>'edit', 'model'=>$model]) 
        <div class="form-actions">
            <a href="{{ route('admin.pages.index') }}" class="btn btn-default pull-left"><i class="fa fa-reply"></i> Cancelar</a>
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar cambios</button>
        </div>
    </form>

</section>
@endsection