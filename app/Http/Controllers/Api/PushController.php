<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class PushController extends BaseController {

    public function registerPushToken(Request $request) {
        $pushToken = $request->get('push_token', null);
        $jsonResponse = $this->getResponseInstance();

        $token = $this->getToken();

        if ($token) {
            if ($pushToken) {
                $userToken = \App\Models\UserToken::where(['token' => $token])->first();
                if ($userToken) {
                    \Illuminate\Support\Facades\DB::table('user_tokens')->where('id', '!=', $userToken->id)->where('push_token', '=', $pushToken)->delete();
                    $userToken->push_token = $pushToken;
                    $userToken->save();
                    $jsonResponse->setStatusMessage('Token saved');
                    $jsonResponse->setPayload(['token' => $userToken->push_token]);
                } else {
                    
                }
            } else {
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setStatusMessage('Token not received!');
            }
        } else {
            $jsonResponse->setHttpCode(403);
        }
        return $this->renderResponse();
    }

    public function removePushToken(Request $request) {
        $jsonResponse = $this->getResponseInstance();
        $token = $this->getToken();

        if ($token) {
            $userToken = \App\Models\UserToken::where(['token' => $this->getToken()])->first();
            $userToken->push_token = null;
            $userToken->save();
            $jsonResponse->setStatusMessage('Token removed');
        } else {
            $jsonResponse->setHttpCode(403);
        }
        return $this->renderResponse();
    }

}
