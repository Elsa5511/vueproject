<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Notification as AppNotification;
use App\Notifications\GenericNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Description of TestController
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class TestController extends BaseController
{

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    function haversineGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371
    ) {
        $pi80 = M_PI / 180;
        $latitudeFrom *= $pi80;
        $longitudeFrom *= $pi80;
        $latitudeTo *= $pi80;
        $longitudeTo *= $pi80;

        $r = 6372.797; // mean radius of Earth in km
        $dlat = $latitudeFrom - $longitudeFrom;
        $dlon = $latitudeTo - $longitudeTo;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($latitudeFrom) * cos($latitudeTo) * sin($dlon / 2) * sin($dlat / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        //echo '<br/>'.$km;
        return $km;
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit = 'MT')
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "MT") {
            return ($miles * 1.609344) * 1000;
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function test(\Illuminate\Http\Request $request, \Illuminate\Http\Response $response)
    {


        $response = $this->getResponseInstance();



        return User::findOrFail('19ba69e3-51da-42ff-be2e-ac15a80e8b10')->refereds;



        $to = $request->get('to');
        $results = null;
        switch ($to) {
            case 'providers_all':
                $results = User::where(['role' => 'PROVIDER', 'status' => 'ACTIVE'])->get();
                break;
            case 'providers_online':
                $results = User::where(['role' => 'PROVIDER', 'presence' => 'ONLINE'])->get();
                break;
            case 'providers_offline':
                $results = User::where(['role' => 'PROVIDER', 'presence' => 'OFFLINE'])->get();
                break;
            case 'users_all':
                $results = User::where(['role' => 'USER', 'status' => 'ACTIVE'])->get();
                break;
            case 'users_online':
                $results = User::where(['role' => 'USER', 'presence' => 'ONLINE'])->get();
                break;
            case 'users_offline':
                $results = User::where(['role' => 'USER', 'presence' => 'OFFLINE'])->get();
                break;
        }

        if ($results) {
            $data = new AppNotification();
            $data->setTitle('Vaceate.com');
            $data->setMessage($request->get('message'));
            $data->setSenderId(User::where(['role'=>'ADMIN'])->firstOrFail()->id);
            $notification = new GenericNotification(User::where(['role'=>'ADMIN'])->firstOrFail(), $data, 'NOTIFICATION');
            Notification::send($results, $notification);
            $response->setPayload(['success' => true]);
            $response->setStatusMessage('Notificaciones enviadas correctamente');
        }

        return $this->renderResponse();



        event(new \App\Events\AdminEvent('RESERVATION_ADDED', \App\Models\Appointment::with(['provider', 'customer'])->first()));
        return;


        $messages = Cache::rememberForever('automessages', function () {
            return \App\Models\AutoMessage::all();
        });
        $message = $messages[0];
        $message->message = \App\Lib\TemplateParser::parseTemplate($message->message, ['user' => \App\Models\User::findOrFail(1)->with('profile')->toArray()]);
        dd($message);



        $req = Request::create('/api/v1', 'GET', []);
        $req->replace($request->all());
        // Dispatch request.
        $response = \Illuminate\Support\Facades\Route::dispatchToRoute($req);

        return $response;

        \geoPHP::geosInstalled();
        $point = \geoPHP::load("Point(-87.9363532 42.0465078)", "wkt");
        $point2 = \geoPHP::load("Point(-87.9856006 42.050121)", "wkt");
        $difference = $point2->distance($point);
        //dd($difference);
        dd($this->distance($point->getY(), $point->getX(), $point2->getY(), $point2->getX(), 'Mt'));

        //$sms= \App\Lib\SMSManager::getInstance()->sendSMS('+12243186138', 'Hola carajo!');
        //return (new \App\Lib\Api\JSONResponse($sms->lastResponse->))->render();
        /* $redis = Redis::connection('pubsub');
          return $redis->publish('events.1', json_encode(['foo' => 'bar'])); */

        $user = \App\Models\User::where(['phone' => '2243186138'])->firstOrFail();
        $notification = new \App\Models\Notification('Test Notification');
        $notification->setSenderId($user->id);
        $notification->setMessage('Test body');
        $notification->setIcon($user->medium_avatar_url);
        $notification->setAction('/profile');
        $user->notify(new \App\Notifications\GenericNotification($user, $notification));

        return null;

        $devices = \App\Models\UserToken::select('push_token')->where('push_token', '!=', null)->get();
        $tokens = [];
        foreach ($devices as $d) {
            $tokens[] = $d->push_token;
        }

        try {
            $push = new \Cypretex\PushNotification\PushNotification('fcm');
            $message = $push->setMessage([
                'data' => [
                    'notification' => [
                        'title' => 'This is the title',
                        'icon' => 'http://vaceate.test/public/apps/frontend/assets/icon/android-chrome-512x512.png',
                        'body' => 'Este es el cuerpo 😀 del mensaje <img src="https://raw.githubusercontent.com/EddyVerbruggen/nativescript-plugin-firebase/master/docs/images/features/notifications.png" height="85px" alt="Notifications"/>',
                        'actions' => [
                            ["action" => "Yes", "title" => "Yes ⛵"],
                            ["action" => "No", "title" => "NO"]
                        ]
                    ],
                    'extraPayLoad1' => 'value1',
                    'extraPayLoad2' => 'value2',
                ],
                'webpush' => [
                    "fcm_options" => [
                        "link" => "https://dummypage.com"
                    ]
                ]
            ])
                ->setDevicesToken($tokens)
                ->send();
            $resp = $this->getResponseInstance();
            $resp->setPayload(($message->getFeedback()));
            return $resp->render();
        } catch (\Exception $ex) {
            dd($ex);
        }
        /*
          $user= \App\Models\User::find('00574c6c-5d44-3973-be1d-cd9655fc4f4e');
          $notification = new \App\Models\Notification("Test");
          $notification->setMessage("Esto es una prueba");
          $user->notify(new \App\Notifications\GenericNotification($user, $notification)); */
    }
}
