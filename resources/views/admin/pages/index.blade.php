@extends('admin.layouts.app')
@section('page.title') Paginas @endsection
@section('content')



{!! $dataTable->table(['class' => 'table table-bordered table-striped dataTable', 'id' => 'pages-table']) !!}
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

@endpush
