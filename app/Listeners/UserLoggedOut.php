<?php

namespace App\Listeners;

use App\Events\Event;

class UserLoggedOut {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Event  $event
     * @return void
     */
    public function handle($event) {
        $user = $event->user;
        if (!$user) {
            return;
        }
        if ($user->role === 'PROVIDER') {
            $user->presence = 'OFFLINE';
            $user->save();
            event(new \App\Events\UserStatusChanged($user));
        }
    }

}
