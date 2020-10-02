<?php

namespace Cypretex\Chat\Messages;

use Cypretex\Chat\Models\Conversation;

class SendMessageCommand {

    public $senderId;
    public $body;
    public $conversation;
    public $type;
    public $sent_at;

    /**
     * @param Conversation $conversation The conversation
     * @param string $body The message body
     * @param int $senderId The sender identifier
     * @param string $tempId Temporal id sent from client
     * @param string $type The message type
     */
    public function __construct(Conversation $conversation, $body, $senderId, $tempId = null, $type = 'text', $sent_at = 0) {
        $this->conversation = $conversation;
        $this->body = $body;
        $this->tempId = $tempId == null ? \Ramsey\Uuid\Uuid::uuid4() : $tempId;
        $this->type = $type;
        $this->senderId = $senderId;
        $this->sent_at = $sent_at > 0 ? $sent_at : time();
    }

}
