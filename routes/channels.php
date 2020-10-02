<?php

/*
  |--------------------------------------------------------------------------
  | Broadcast Channels
  |--------------------------------------------------------------------------
  |
  | Here you may register all of the event broadcasting channels that your
  | application supports. The given channel authorization callbacks are
  | used to check if an authenticated user can listen to the channel.
  |
 */

use Cypretex\Chat\Facades\ChatFacade as Chat;

//Broadcast::routes(['middleware' => ['api']]);

Broadcast::channel('presence.{role}', function($user, $role) {
    if (!$user) {
        return false;
    }
    return $user->role === $role || $user->role === 'ADMIN';
});

Broadcast::channel('presence.{role}', function($user, $role) {
    if (!$user) {
        return false;
    }
    return $user->role === $role;
});

Broadcast::channel('all', function($user) {
    if ($user) {
        return ['id' => $user->id, 'name' => $user->name, 'role' => $user->role, 'presence' => $user->presence, 'work_status' => $user->work_status];
    }
    return [];
});

Broadcast::channel('role-notifications.{role}', function($user, $role) {

    if ($user) {
        return $user->role === $role || $user->role === 'ADMIN';
    }
    return false;
});

Broadcast::channel('notifications.{id}', function($user, $id) {

    if ($user) {
        return $user->id === $id || $user->role === 'ADMIN';
    }
    return false;
});


Broadcast::channel('App.User.{id}', function ($user, $id) {
    if (!$user) {
        return false;
    }
    return $user->id === $id || $user->role === 'ADMIN';
});


Broadcast::channel('mc-chat-conversation.{id}', function ($user, $id) {
    if (!$user) {
        return false;
    }
    $conversation = Chat::conversations()->getById($id);
    if (!$conversation) {
        return false;
    }
    return $user->can('join-conversation', $conversation);
});

Broadcast::channel('positions.{id}', function($user, $id) {
    if (!$user) {
        return false;
    }

    if ($user->id === $id || $user->role === 'ADMIN') {
        return true;
    }

    $appointment = \App\Models\Appointment::where(['finished' => false, 'status_name' => 'ON_THE_WAY'])->where(function($q) use ($id) {
                $q->where(['customer_id' => $id])
                        ->orWhere(['provider_id' => $id]);
            })->first();

    return $appointment;
});

Broadcast::channel('appointment-positions.{id}', function($user, $id) {
    if (!$user) {
        return false;
    }
    $reservation = App\Models\Appointment::find($id);
    if (!$reservation) {
        return false;
    }
    return $user->can('watch-position', $reservation);
});

Broadcast::channel('admin.events', function($user) {
    if (!$user) {
        return false;
    }
    return $user->role == 'ADMIN';
});



Broadcast::channel('admin.positions', function($user) {
    if (!$user) {
        return false;
    }
    return $user->role == 'ADMIN';
});

