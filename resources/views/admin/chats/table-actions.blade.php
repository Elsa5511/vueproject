<div class="text-center">
    <a class="btn btn-default btn-xs btn-primary" href="{{ route('admin.chats.show', ['id'=>$id])}}"><i class="fa fa-edit"></i></a>
    <a class="btn btn-default btn-xs btn-danger btn-delete" href="#" data-action='{{ route('admin.chats.destroy', ['id'=>$id])}}'><i class="fa fa-trash"></i></a>
</div>