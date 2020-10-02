<div class="text-center">
    <a class="btn btn-default btn-xs btn-primary" href="{{ route('admin.withdrawals.edit', ['id'=>$id])}}"><i class="fa fa-edit"></i></a>
    <a class="btn btn-default btn-xs btn-danger btn-delete" href='{{$id}}' data-title="Cancel?" data-text="Do you want to cancel this withdrawal request?"><i class="fa fa-trash"></i></a>
</div>