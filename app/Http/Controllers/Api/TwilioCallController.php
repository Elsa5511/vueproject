<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TwilioCallController extends BaseController {

    public function makeCall(Request $request) {
        $userFrom = $this->getUser();
        $userTo = \App\Models\User::findOrFail($request->get('to'));
        $jsonResponse = $this->getResponseInstance();




        /*
          $appointment = \App\Models\Appointment::where(['finished' => false])->where(function($q) use ($userFrom, $userTo) {
          $q->where(['customer_id' => $userFrom->id, 'provider_id' => $userTo->id])
          ->orWhere(['customer_id' => $userTo->id, 'provider_id' => $userFrom->id]);
          })->first();

          if (!$appointment) {
          $jsonResponse->setHttpCode(403);
          $jsonResponse->setStatusMessage('Debe tener una cita activa para poder realizar llamadas a este usuario.');
          return $this->renderResponse();
          }

         */

        if (!$userTo) {
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setStatusMessage('Debe seleccionar el telefono a llamar!');
        } else {

            if (!$userFrom->phone || !$userTo->phone) {
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setStatusMessage('No se puede realizar la llamada, tú o el usuario destino no tienen teléfono registrado.');
                return $this->renderResponse();
            }

            if (!$userFrom->can('make-call', $userTo)) {
                $jsonResponse->setHttpCode(403);
                $jsonResponse->setStatusMessage('Debe tener una cita activa para poder realizar llamadas a este usuario.');
                return $this->renderResponse();
            }

            try {
                $call = \App\Lib\CallManager::getInstance()->makeCall($userFrom->completePhone(), $userTo->completePhone());
                $jsonResponse->setStatusMessage('Estamos llamando, por favor conteste la llamada...');
                $jsonResponse->setPayload($call);
            } catch (\Exception $ex) {
                $jsonResponse->setHttpCode(500);
                $jsonResponse->setStatusMessage($ex->getMessage());
                $jsonResponse->setPayload($ex);
            }
        }
        return $this->renderResponse();
    }

    public function responseCall(Request $request) {
        $to = $request->get('to');
        header("content-type: text/xml");
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response><Dial>+$to</Dial></Response>";
    }

}
