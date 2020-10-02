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

@if (session('status'))
<div class="alert alert-success">
    {!! session('status') !!}
</div>
@endif

@if(session()->has('message'))
<div class="messages-container">
    <div class="alert alert-{{session()->get('message')->getType()}}">
        {!! session()->get('message')->getText() !!}
    </div>
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

@if($errors->any())
<div class="messages-container">
    @foreach ($errors->all() as $error)
    <div class="alert alert-danger">
        {!! $error !!}
    </div>
    @endforeach
</div>
@endif