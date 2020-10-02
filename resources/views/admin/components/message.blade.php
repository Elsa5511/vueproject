@if(is_string($message))
<div class="alert alert-success" role="alert">
    {!! $message !!}
</div>
@else
<div class="alert alert-{{$message->getType()}} @if($message->isCloseable()) alert-dismissible @endif" role="alert">
    @if($message->getIcon())
    <i class='fa fa-{{$message->getIcon()}} top-message-icon'></i>
    @endif
    {!! $message->getText() !!}
    @if($message->isCloseable())
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    @endif
</div>
@endif