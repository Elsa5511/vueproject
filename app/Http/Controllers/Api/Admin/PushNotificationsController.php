<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\GenericNotification;
use App\Models\Notification as AppNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Description of AutomessagesController
 *
 * @author pablo
 */
class PushNotificationsController extends \App\Http\Controllers\Api\BaseController
{
    public function send(Request $request)
    {
        $response = $this->getResponseInstance();
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
            default:
                $response->setHttpCode(400);
                $response->setPayload(['success' => false]);
                $response->setStatusMessage('Selecciona el destino y el mensaje');
                return $response->render();
        }

        if ($results) {
            $data = new AppNotification();
            $data->setTitle('Vaceate.com');
            $data->setMessage($request->get('message'));
            $data->setSenderId(User::where(['role' => 'ADMIN'])->firstOrFail()->id);
            $data->setDestType('mobile');
            $notification = new GenericNotification(User::where(['role' => 'ADMIN'])->firstOrFail(), $data, 'NOTIFICATION');
            Notification::send($results, $notification);

            $data->setDestType('browser');
            $notification = new GenericNotification(User::where(['role' => 'ADMIN'])->firstOrFail(), $data, 'NOTIFICATION');
            Notification::send($results, $notification);

            $response->setPayload(['success' => true]);
            $response->setStatusMessage('Notificaciones enviadas correctamente');
        }

        return $this->renderResponse();
    }
}
