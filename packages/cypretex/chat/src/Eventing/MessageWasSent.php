<?php

namespace Cypretex\Chat\Eventing;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Cypretex\Chat\Models\Message;
use Cypretex\Chat\Models\MessageNotification;

class MessageWasSent implements ShouldBroadcast
{
    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->message->sender;

        $this->createNotifications();
    }

    /**
     * Creates an entry in the message_notification table for each participant
     * This will be used to determine if a message is read or deleted.
     */
    public function createNotifications()
    {
        MessageNotification::make($this->message, $this->message->conversation);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('mc-chat-conversation.' . $this->message->conversation->id);
    }
}
