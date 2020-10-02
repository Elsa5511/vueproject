@extends('admin.layouts.app')
@section('page.title') Detalle de chat @endsection
@push('styles')

<style>
    #chatWrapper{
        height: 500px;
        border: 1px solid #ddd;
        overflow-y: scroll;
    }

    .chat img.profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        position: absolute;
        bottom: 10px;
        border: 1px solid #ededed;
    }

    .chat img.profile-pic.left {
        left: 10px;
    }

    .chat img.profile-pic.right {
        right: 10px;
    }

    .chat img.profile-pic.center{
        left: 50%;
        margin-left: -20px;
        top: 0;
        z-index: 1;
        border: none;
        position: relative;
        margin-bottom: -10px;
    }



    .chat .message {
        font-size: 14px;
        white-space: pre-line;
    }

    .chat .message-detail {
        white-space: nowrap;
        font-size: 14px;
    }

    .chat .bar.item-input-inset .item-input-wrapper input {
        width: 100% !important;
    }

    .chat .message-wrapper {
        position: relative;
        overflow: hidden;
    }

    .chat .message-wrapper:last-child {
        margin-bottom: 10px;
    }

    .chat .chat-bubble {
        border-radius: 5px;
        display: inline-block;
        padding: 10px 18px;
        position: relative;
        margin: 10px;
        max-width: 80%;
    }

    .chat .chat-bubble:before {
        content: "\00a0";
        display: block;
        height: 16px;
        width: 9px;
        position: absolute;
        bottom: -7.5px;
    }

    .chat .chat-bubble.left {
        background-color: #e6e5eb;
        float: left;
        margin-left: 55px;
    }

    .chat .chat-bubble.center {
        background-color: #f00;
        float: none;
        margin-top: 55px;
        color: #fff;
        text-align: center;
        margin: 0 auto;
        display: block;
        max-width: 550px;
        margin-bottom: 20px;
    }

    .chat .chat-bubble.left:before {
        background-color: #e6e5eb;
        left: 10px;
        -webkit-transform: rotate(70deg) skew(5deg);
    }

    .chat .chat-bubble.right {
        background-color: #158ffe;
        color: #fff;
        float: right;
        margin-right: 55px;
    }

    .chat .chat-bubble.right:before {
        background-color: #158ffe;
        right: 10px;
        -webkit-transform: rotate(118deg) skew(-5deg);
    }

    .chat .chat-bubble.right a.autolinker {
        color: #fff;
        font-weight: bold;
    }

    .chat .user-messages-top-icon {
        font-size: 28px;
        display: inline-block;
        vertical-align: middle;
        position: relative;
        top: -3px;
        right: 5px;
    }

    .chat .msg-header-username {
        display: inline-block;
        vertical-align: middle;
        position: relative;
        top: -3px;
    }

    .chat input, .chat textarea, .chat .item-input, .chat .item-input-wrapper {
        background-color: #f4f4f4 !important;
    }

    .chat .bold {
        font-weight: bold;
    }

    .chat .cf {
        clear: both !important;
    }

    .chat  a.autolinker {
        color: #3b88c3;
        text-decoration: none;
    }

    .send-message{
        overflow: hidden;
        
    }
    #message-to-send{
        float: left;
        margin: 10px 0;
        resize: none;
        height: 40px;
        width: 100%;
        border: 1px solid #ededed;
        
    }
    #sendMessageBtn{
        float: right;
    }
</style>

@endpush
@section('content')
<section class="content"> 

    <div id="chatWrapper" class="chat"></div>
    <div class="send-message clearfix">
        

        <textarea name="message-to-send" id="message-to-send" placeholder ="Enviar un mensaje" rows="1"></textarea>

        <button id="sendMessageBtn" class="btn btn-success">Enviar</button>

    </div> <!-- end chat-message -->
</section>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
<script>
    var messages = {!! json_encode($model->messages()->orderBy('updated_at', 'asc')->get()) !!}
    ;

    var messagesContainer = $('#chatWrapper');
    
    function sendTextMessage() {
      var text = $('#message-to-send').val()
      $.ajax({
         url: '/api/v1/chat/{{$model->id}}/messages',
         type: 'POST',
         headers:{
              Authorization: 'Bearer ' + USER_TOKEN
         },
         data: {
             body:text,
             type:'text',
         },
         success:function(data){
             
         },
         error:function(error){
             alert('Error!');
         }
      });
      $('#message-to-send').val('')
    }

    $('#message-to-send').keypress(function(e) {
      if(e.which == 13) {
          sendTextMessage();
          return false;
      }
    });

    $('#sendMessageBtn').on('click', function() {
      sendTextMessage()
    })
    
    function scrollDown(){
        messagesContainer.animate({ scrollTop: messagesContainer.prop("scrollHeight")}, 1000);
    }

    function addMessage(message) {
        var type = message.type;
        var body = null;
        var cssClass = 'left';

        switch (type) {
            case 'text':
                body = message.body;
                break;
        }
        if (body) {
            switch (message.sender.role) {
                case 'PROVIDER':
                    cssClass = 'left';
                    break;
                case 'USER':
                    cssClass = 'right';
                    break;
                default:
                    cssClass = 'center';
                    break;
            }
            var msgWrp = $('<div class="message-wrapper"></div>');
            var msgContent = $('<div class="chat-bubble slide-' + cssClass + ' ' + cssClass + '"><div class="message">' + body + '</div></div>');
            var msgAvatar = $('<img class="profile-pic ' + cssClass + '" src="' + message.sender.small_avatar_url + '">');
            var msgDetail = $('<div class="message-detail"><span class="bold">' + message.sender.name + '</span>, <span>' +  message.created_at + '</span></div>');
            msgContent.append(msgDetail);
             msgWrp.append(msgAvatar);
            msgWrp.append(msgContent);
           
            messagesContainer.append(msgWrp);
        }
    }

    $(document).ready(function () {
        EchoClient.private('mc-chat-conversation.{{ $model->id }}')
                .listen('.Cypretex\\Chat\\Eventing\\MessageWasSent', function (data) {
                    console.log(data.message);
                    addMessage(data.message);
                    scrollDown();
                });
        Object.keys(messages).map(function (i) {
            var m = messages[i];
            addMessage(m);
        });
        scrollDown();

    })

</script>
@endpush

