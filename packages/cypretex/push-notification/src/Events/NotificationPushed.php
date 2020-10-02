<?php

namespace Cypretex\PushNotification\Events;

use Cypretex\PushNotification\PushNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationPushed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \Cypretex\PushNotification\PushNotification
     */
    public $push;

    /**
     * Create a new event instance.
     *
     * @param  \Cypretex\PushNotification\PushNotification $push
     */
    public function __construct(PushNotification $push)
    {
        $this->push = $push;
    }
}
