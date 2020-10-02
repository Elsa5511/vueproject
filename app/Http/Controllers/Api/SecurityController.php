<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;

class SecurityController extends BaseController {

    public function changePassword(Request $request) {
        $fields = $request->only('password', 'new_password', 'new_password_confirmation');


        $response = $this->getResponseInstance();
        $validator = Validator::make($fields, [
                    'password' => 'required|string|min:6',
                    'new_password' => 'required|string|min:6|confirmed',
        ]);
        $response->setValidator($validator);
        if ($validator->fails()) {
            $response->setValidator($validator);
            $response->setHttpCode(400);
            $response->setSuccess(false);
            $response->setStatusMessage(__('auth.validator_fails'));
            return $this->renderResponse();
        } elseif (!Hash::check($fields['password'], $this->getUser()->password)) {

            $response->setHttpCode(400);
            $response->setSuccess(false);
            $response->setStatusMessage(__('auth.validator_fails'));
            $response->getValidationErrors()->add('password', __('auth.wrong_password'));
            return $this->renderResponse();
        }

        $user = $this->getUser();
        $user->password = \Illuminate\Support\Facades\Hash::make($fields['new_password']);
        $user->save();
        $response->setStatusMessage(__('generics.data_saved_successfully'));
        return $this->renderResponse();
    }

}
