<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Description of ChatAdminMessageEvent
 *
 * @author pablo
 */
class AdminEvent implements ShouldBroadcast {

    public $payload;
    public $type;

    public function __construct($type, $payload) {
        $this->payload = $payload;
        $this->type = $type;
    }

    public function broadcastAs() {
        return 'event';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn() {
        return new PrivateChannel('admin.events');
    }

}
