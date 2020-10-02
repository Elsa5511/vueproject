<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Cypretex\Chat\Facades\ChatFacade as Chat;

class HomeController extends BaseController
{

    public function index(Request $request)
    {
        $response = $this->getResponseInstance();
        $response->setStatusMessage(__('generics.api_welcome', ['version' => config('app.api_version'), 'name' => config('app.name')]));
        return $this->renderResponse();
    }

    public function load(Request $request)
    {
        $payload = ['config' => config('custom')];
        $user = $this->getUser();
        if (!$user || $user->role == 'GUEST') {
            $response = $this->getResponseInstance();
            $response->setPayload($payload);
            return $this->renderResponse();
        }

        /*
          $permissions = [
          'view_reservations' => false,
          'make_reservation' => false,
          'create_chat' => false,
          'view_providers' => false,
          'edit_profile' => false,
          'edit_account' => false,
          'edit_prices' => false,
          'restrict_locations' => false,
          ];


          $payload['permissions'] = $permissions; */


        $conversations = Chat::conversations()->for($user)->limit(200)->page(1)->get();
        $conversationsToSend = [];
        foreach ($conversations as $conversation) {
            if ($conversation->last_message) {
                $conversation->unread_messages_count = Chat::conversation($conversation)->for($user)->unreadCount();
                $users = $conversation->users()->get();
                $conversation->users = $users;
                $add = true;
                $messages = Chat::conversation($conversation)->for($user)->getMessages();
                if (!count($messages)) {
                    $add = false;
                } else {
                    foreach ($users as $cUser) {
                        if ($this->getUser()->id != $cUser->id) {
                            if ($this->isUserInBlockedZone($cUser)) {
                                $add = false;
                            }
                        }
                    }
                }
                if ($add) {
                    $conversationsToSend[] = $conversation;
                }
            }
        }

        $payload['conversations'] = $conversationsToSend;


        if ($user->role == 'ADMIN') {
            $payload['stats'] = $this->getStats($request);
        }




        $response = $this->getResponseInstance();
        $response->setPayload($payload);
        return $this->renderResponse();
    }

    public function stats(Request $request)
    {
        $response = $this->getResponseInstance();
        $response->setPayload($this->getStats($request));
        return $this->renderResponse();
    }


    private function getStats(Request $request)
    {
        $stats = [
            'reservations' => [
                'pending' => 0,
                'active' => 0,
                'unchecked' => 0,
                'finalized' => 0,
                'all' => 0,
            ]
        ];

        $records = \App\Models\Appointment::where([]);

        $stats['reservations']['active'] = \App\Models\Appointment::where(['status_name' => 'ON_THE_WAY'])->orWhere(['status_name' => 'IN_PROGRESS'])->count();
        $stats['reservations']['pending'] = \App\Models\Appointment::where(['status_name' => 'PENDING'])->orWhere(['status_name' => 'AWAITING_ACCEPTANCE'])->count();
        $stats['reservations']['finalized'] = \App\Models\Appointment::where(['finished' => 1])->count();
        $stats['reservations']['all'] = \App\Models\Appointment::count();
        $stats['reservations']['unchecked'] = \App\Models\Appointment::where('checked_at', '=', '1970-01-01 00:00:01')->count();

        return $stats;
    }

    public function usersOnline(Request $request)
    {
        $role = '';
        if ($this->getUser()) {
            if ($this->getUser()->role === 'PROVIDER') {
                $role = 'USER';
            } else if ($this->getUser()->role === 'USER') {
                $role = 'PROVIDER';
            }
        }
        $usersOnline = \App\Models\User::where(['presence' => 'ONLINE']);
        if ($role) {
            $usersOnline->where(['role' => $role]);
        }
        $usersOnline = $usersOnline->get();
        $payload = [];
        foreach ($usersOnline as $user) {
            $payload[] = $user->transformToPresence();
        }
        $response = $this->getResponseInstance();
        $response->setPayload($payload);
        return $this->renderResponse();
    }
}
