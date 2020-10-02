<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;

/**
 * Description of UserEventSubscriber
 *
 * @author pablo
 */
class UserEventSubscriber {

    /**
     * Handle user login events.
     */
    public function onUserLogin($event) {
        $user = $event->user;
        if (!$user || $user->role == 'GUEST') {
            return;
        }
        $user->presence = 'ONLINE';
        $user->save();
        event(new \App\Events\UserStatusChanged($user));
    }

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event) {
        $user = $event->user;
        if (!$user || $user->role == 'GUEST') {
            return;
        }
        $user->presence = 'OFFLINE';
        $user->save();
        event(new \App\Events\UserStatusChanged($user));
    }

    public function onUserStatusChanged($event) {
        $onlineUsers = Cache::get('users_online', []);
        $user = $event->user;
        if ($user->presence != 'ONLINE') {
            
        } else {
            
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events) {
        $events->listen(
                'Illuminate\Auth\Events\Login', 'App\Listeners\UserEventSubscriber@onUserLogin'
        );

        $events->listen(
                'Illuminate\Auth\Events\Logout', 'App\Listeners\UserEventSubscriber@onUserLogout'
        );

        $events->listen(
                'App\Events\UserStatusChanged', 'App\Listeners\UserEventSubscriber@onUserStatusChanged'
        );
    }

}
