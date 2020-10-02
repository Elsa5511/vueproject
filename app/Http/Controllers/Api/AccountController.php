<?php

namespace App\Http\Controllers\Api;

use \Illuminate\Http\Request;

class AccountController extends BaseController {
    
    

    public function changePassword(Request $request) {
        $user = $this->getUser();
        $jsonResponse = $this->getResponseInstance();
        if (!$user) {
            $jsonResponse->setHttpCode(403);
            $jsonResponse->setStatusMessage('El usuario no existe!');
        } else {

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                        'old_password' => 'required|min:6',
                        'new_password' => 'required|confirmed|min:6',
            ]);
            if (!\Illuminate\Support\Facades\Hash::check($request->get('old_password'), $user->password)) {
                $jsonResponse->setHttpCode(403);
                $jsonResponse->setStatusMessage('La contraseña actual no es correcta!');
                return $jsonResponse->render();
            }

            if ($validator->fails()) {
                $jsonResponse->setValidator($validator);
                $jsonResponse->setStatusMessage('Los campos no son correctos');
                $jsonResponse->setHttpCode(400);
                return $jsonResponse->render();
            } else {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->get('new_password'));
                $user->save();
                $jsonResponse->setStatusMessage('Se ha cambiado su contraseña correctamente!');
                return $jsonResponse->render();
            }
        }
        return $jsonResponse->render();
    }

}
