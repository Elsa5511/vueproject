<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\User;

class UserStatusChanged implements ShouldBroadcast {

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     *
     * @var User
     */
    public $user = null;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user) {
        $this->user = $user;
    }

    public function broadcastAs() {
        return 'statusChanged';
    }

    public function broadcastWith() {
        return ['user' => $this->user->transformToPresence()];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new Channel('presence.all');
    }

}
