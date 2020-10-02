@if(session()->has('messages'))
<div class="messages-container">
    @foreach (session()->get('messages') as $message)
    <div class="alert alert-{{$message->getType()}}">
        {!! $message->getText() !!}
    </div>
    @endforeach
    {!! session()->forget('messages') !!}
</div>
@endif

@if(isset($messages) && count($messages))
<div class="messages-container">
    @foreach ($messages as $message)
    <div class="alert alert-{{$message->getType()}}">
        {!! $message->getText() !!}
    </div>
    @endforeach
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible">
    {!! session('error') !!}
</div>
@endif

@if (session('status'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {!! session('status') !!}
</div>
@endif

@if(session()->has('message'))
<div class="messages-container">
    @if(is_string(session()->get('message')))
    <div class="alert alert-success">
        {!! session()->get('message') !!}
    </div>
    @else
    <div class="alert alert-{{session()->get('message')->getType()}}">
        {!! session()->get('message')->getText() !!}
    </div>
    @endif
</div>
@endif

@if(session()->has('vars.message'))
<div class="messages-container">
    <div class="alert alert-{{session()->get('vars.message')->getType()}}">
        {!! session()->get('vars.message')->getText() !!}
    </div>
</div>
@endif

@if(session()->has('var.messages'))
<div class="messages-container">
    @foreach (session()->get('var.messages') as $message)
    <div class="alert alert-{{$message->getType()}}">
        {!! $message->getText() !!}
    </div>
    @endforeach
</div>
@endif