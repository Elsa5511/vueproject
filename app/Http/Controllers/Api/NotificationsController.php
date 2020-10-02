<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserProfileCollection;

class NotificationsController extends BaseController {

    public function all(Request $request) {
        $payload= $this->getUser()->notifications;
        $response=$this->getResponseInstance();
        $response->setPayload($payload);
        return $this->renderResponse();
    }

    public function unread(Request $request) {
        $payload= $this->getUser()->unreadNotifications;
        $response=$this->getResponseInstance();
        $response->setPayload($payload);
        return $this->renderResponse();
    }

    public function read() {
        $this->getUser()->unreadNotifications->markAsRead();
    }

}
