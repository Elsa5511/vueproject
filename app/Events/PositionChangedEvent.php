<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Position;

class PositionChangedEvent implements ShouldBroadcast {

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    private $position;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Position $position) {
        $this->position = $position;
    }

    public function broadcastAs() {
        return 'positionChanged';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('admin.positions');
    }

    public function broadcastWith() {
        return ['position' => $this->position->toArray(), 'user' => \App\Models\User::find($this->position->user_id)];
    }

}
