<?php

namespace App\Listeners;

use App\Events\Event;

class UserLoggedIn {

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
            $user->presence = 'ONLINE';
            //$user->work_status = 'AVAILABLE';
            $user->save();
            
        }
        event(new \App\Events\UserStatusChanged($user));
    }

}
