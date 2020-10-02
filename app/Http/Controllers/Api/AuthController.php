<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Cypretex\Chat\Facades\ChatFacade as Chat;

class AuthController extends BaseController
{

    public function register(Request $request, Response $response)
    {
        $data = $request->only('name', 'email', 'gender', 'password', 'dob', 'phone', 'country', 'password_confirmation', 'first_name', 'last_name', 'identity_id', 'identity_country', 'role', 'avatar', 'referer_code');
        $jsonResponse = $this->getResponseInstance();
        $validator = $this->validator($data);

        if ($validator->fails()) {
            $jsonResponse->setValidator($validator);
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage(__('auth.validator_fails'));
            return $this->renderResponse();
        }


        DB::beginTransaction();
        try {
            $user = $this->create($data);
            $user->referer_code;
            $key = 'email_registration_send';
            /* if ((\App\Lib\SettingsManager::getValue('user_require_phone_validation', 0, 'bool', true) && isset($data['phone']))) {
              $key = 'phone_registration_send';
              $this->sendVerificationCode($user->phone, $user->country_id);
              $jsonResponse->setNextAction('VALIDATION_CODE_REQUIRED');
              } else */
            if ((\App\Lib\SettingsManager::getValue('user_require_email_validation', 0, 'bool', true)) && isset($data['email'])) {
                $this->sendVerificationCode($user->email);
                $jsonResponse->setNextAction('VALIDATION_CODE_REQUIRED');
            } else {
                $key = 'registration_success';
            }

            if (isset($data['email'])) {
                $credentials = ['email' => $data['email'], 'password' => $data['password']];
            } else {
                $credentials = ['phone' => $data['phone'], 'password' => $data['password'], 'country_id' => $data['country']];
            }

            $jsonResponse->setStatusMessage(__('auth.' . $key));


            if (!$token = auth('api')->attempt($credentials)) {
                $jsonResponse->setValidator($validator);
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setSuccess(false);
                $jsonResponse->setStatusMessage(__('auth.failed'));
                return $this->renderResponse();
            }

            $this->sendTokenResponse($jsonResponse, $token, $user);
            event(new \Illuminate\Auth\Events\Login('api', $this->getUser(), false));
            DB::commit();
            return $this->renderResponse();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public function validateRegistration(Request $request)
    {
        $data = $request->only('name', 'email', 'gender', 'password', 'dob', 'phone', 'country', 'password_confirmation', 'first_name', 'last_name', 'identity_id', 'identity_country', 'role', 'avatar', 'referer_code');
        $jsonResponse = $this->getResponseInstance();
        $validator = $this->validator($data, false);

        if ($validator->fails()) {
            $jsonResponse->setValidator($validator);
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage(__('auth.validator_fails'));
            return $this->renderResponse();
        }
        return $this->renderResponse();
    }

    public function resendCode(Request $request, Response $response)
    {
        $data = $request->only('field', 'country');
        $result = $this->sendVerificationCode($data['field'], $data['country']);
        $jsonResponse = $this->getResponseInstance();
        if ($result == 'SENT_BY_PHONE') {
            $jsonResponse->setStatusMessage(__('auth.code_sent_by_phone'));
        } else {
            $jsonResponse->setStatusMessage(__('auth.code_sent_by_email'));
        }
        return $this->renderResponse();
    }

    private function sendVerificationCode($field, $country = null)
    {
        if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
            $user = User::where(['email' => $field, 'email_verified' => false])->first();
            if ($user) {
                $user->email_token = str_random(6);
                $user->save();
                $mail = new \App\Mail\VerifyEmail($user);
                Mail::to($user)->send($mail);
                return 'SENT_BY_EMAIL';
            } else {
                throw new Exception(__('auth.failed'));
            }
        } else {
            $user = User::where(['phone' => $field, 'country_id' => $country, 'phone_verified' => false])->first();
            if ($user) {
                $user->phone_token = str_pad(rand(0, pow(10, 4) - 1), 4, '0', STR_PAD_LEFT);
                $user->save();
                $smsResult = \App\Lib\SMSManager::getInstance()->sendSMS($user->completePhone(), __('auth.verify_phone', ['name' => $user->name, 'code' => $user->phone_token, 'app' => config('app.name')]));
                return 'SENT_BY_PHONE';
            } else {
                throw new Exception(__('auth.failed'));
            }
        }
    }

    public function verify(Request $request, Response $response)
    {
        $data = $request->only('field', 'code', 'country');
        $jsonResponse = $this->getResponseInstance();

        $validator = Validator::make($data, [
            'field' => 'required|string',
            'code' => 'required|string|min:4|max:6'
        ]);

        if ($validator->fails()) {
            $jsonResponse->setValidator($validator);
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage(__('auth.validator_fails'));
            return $this->renderResponse();
        }

        $key = 'invalid_token';
        $verified = false;
        if (filter_var($data['field'], FILTER_VALIDATE_EMAIL)) {
            $user = User::where(['email' => $data['field']])->firstOrFail();
            $code = $user->email_token;

            if ($user->email_verified) {
                $key = 'email_already_verified';
            } else {
                if ($code && $code == $data['code']) {
                    $key = 'email_verification_success';
                    $user->email_verified = true;
                    $user->save();
                    $verified = true;
                }
            }
        } else {
            $user = User::where(['phone' => $data['field'], 'country_id' => $data['country']])->firstOrFail();
            $code = $user->phone_token;
            if ($user->phone_verified) {
                $key = 'phone_already_verified';
            } else {
                if (true || $code && $code == $data['code']) {
                    $key = 'phone_verification_success';
                    $user->phone_verified = true;
                    $user->save();
                    $verified = true;
                }
            }
        }

        if (!$verified) {
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage(__('auth.' . $key));
            return $this->renderResponse();
        } else {
            $jsonResponse->setStatusMessage(__('auth.' . $key));
            $token = auth('api')->login($user);
            $this->sendTokenResponse($jsonResponse, $token, $user);
            event(new \Illuminate\Auth\Events\Login('api', $this->getUser(), false));
            return $this->renderResponse();
        }
    }

    protected function login(Request $request, Response $response)
    {
        if ($this->getToken()) {
            \App\Models\UserToken::where(['token' => $this->getToken()])->delete();
            auth('api')->logout(true);
        }


        $data = $request->only('field', 'password', 'country');
        $jsonResponse = $this->getResponseInstance();


        $validator = Validator::make($data, [
            'field' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            $jsonResponse->setValidator($validator);
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage(__('auth.failed'));
            return $this->renderResponse();
        }
        $credentials = [];
        if (filter_var($data['field'], FILTER_VALIDATE_EMAIL)) {

            $credentials = ['email' => $data['field'], 'password' => $data['password'], 'status' => 'ACTIVE'];

            $user = User::where(['email' => $data['field']])->first();
            if ($user && ($user->email_verified == 0) && $user->role != 'ADMIN' && \App\Lib\SettingsManager::getValue('user_require_email_validation', 1, 'bool', true)) {
                $jsonResponse->setValidator($validator);
                $jsonResponse->setHttpCode(401);
                $jsonResponse->setSuccess(false);
                $jsonResponse->setNextAction('VALIDATION_CODE_REQUIRED');
                $jsonResponse->setStatusMessage(__('auth.email_verified_required'));

                return $this->renderResponse();
            }
        } else {
            $credentials = ['phone' => $data['field'], 'password' => $data['password'], 'country_id' => $data['country']];
            $user = User::where(['phone' => $data['field'], 'country_id' => $data['country']])->first();


            $user->referer_code;

            

            if ($user && ($user->phone_verified == 0) && \App\Lib\SettingsManager::getValue('user_require_phone_validation', 1, 'bool', true)) {
                $jsonResponse->setValidator($validator);
                $jsonResponse->setHttpCode(401);
                $jsonResponse->setSuccess(false);
                $jsonResponse->setStatusMessage(__('auth.phone_verified_required'));
                $jsonResponse->setNextAction('VALIDATION_CODE_REQUIRED');
                return $this->renderResponse();
            }

            
        }

        if (!$user) {
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage(__('auth.failed'));
            return $this->renderResponse();
        }

        if ($user->status != 'ACTIVE') {
            $jsonResponse->setHttpCode(401);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage("Su usuario ha sido desactivado. Si entiende es un error, comunicarse con el soporte.");
            return $this->renderResponse();
        }

        if (!$token = auth('api')->attempt($credentials)) {
            $jsonResponse->setValidator($validator);
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage(__('auth.failed'));
            return $this->renderResponse();
        }

        $this->sendTokenResponse($jsonResponse, $token, $user);
        event(new \Illuminate\Auth\Events\Login('api', $this->getUser(), false));
        return $this->renderResponse();
    }

    protected function loginAsGuest()
    {
        if ($this->getToken()) {
            \App\Models\UserToken::where(['token' => $this->getToken()])->delete();
            auth('api')->logout(true);
        }
        $user = $this->getGuestUser();
        $token = auth('api')->login($user);
        $this->sendTokenResponse($this->getResponseInstance(), $token, $user);
        return $this->renderResponse();
    }

    protected function sendTokenResponse($jsonResponse, $token, $user)
    {
        if ($user->role != 'ADMIN') {
            \App\Models\UserToken::where(['user_id' => $user->id])->delete();
        }
        $user->referer_code;

        \App\Models\UserToken::create([
            'user_id' => $user->id,
            'is_user' => $user->role != 'GUEST',
            'token' => $token,
            'version' => request()->header('X-Version', '0.0.0'),
            'client_type' => request()->header('X-Client', 'unknown'),
            'last_ip' => request()->ip(),
            'attributes' => [
                'Agent' => request()->header('User-Agent'),
            ],
        ]);

        $jsonResponse->setPayload([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => new \App\Http\Resources\UserResource($this->getUser()),
        ]);
    }

    protected function logout(Request $request, Response $response)
    {
        if ($this->getToken()) {
            event(new \Illuminate\Auth\Events\Logout('api', $this->getUser()));
            \App\Models\UserToken::where(['token' => $this->getToken()])->delete();
            auth('api')->logout(true);
        }
        $jsonResponse = $this->getResponseInstance();
        $jsonResponse->setStatusMessage(__('auth.logout_success'));
        return $this->renderResponse();
    }

    protected function forgotPassword(Request $request)
    {
        $data = $request->only('field', 'country');
        $jsonResponse = $this->getResponseInstance();
        $user = null;
        $field = null;
        $key = 'email_reset';
        if (filter_var($data['field'], FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $data['field']];
            $field = $data['field'];
            $user = User::where($credentials)->first();
        } else {
            $key = 'phone_reset';
            $credentials = ['phone' => $data['field'], 'country_id' => $data['country']];
            $user = User::where($credentials)->first();
            if ($user) {
                $field = '+' . $user->country->phonecode . '' . $data['field'];
            }
        }



        if (!$user) {
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setSuccess(false);
            $jsonResponse->setStatusMessage(__('auth.failed'));
            return $this->renderResponse();
        }

        $reset = \App\Models\PasswordReset::firstOrNew([
            'field' => $field
        ]);
        $reset->user_id = $user->id;
        $reset->token = str_random(6);
        $reset->save();
        if ($reset) {
            if ($key == 'email_reset') {
                $mail = new \App\Mail\PasswordResetEmail($user, $reset);
                Mail::to($user)->send($mail);
            } else {

                $smsResult = \App\Lib\SMSManager::getInstance()->sendSMS($user->completePhone(), __('auth.reset_password_sms', ['name' => $user->name, 'code' => $reset->token, 'app' => config('app.name')]));
            }
        }

        $jsonResponse->setStatusMessage(__('auth.' . $key));
        return $this->renderResponse();
    }

    protected function resetPassword(Request $request)
    {

        $data = $request->only('field', 'code', 'country', 'newPassword');
        $jsonResponse = $this->getResponseInstance();

        $user = null;
        $field = $data['field'];
        $mode = 'email';

        if ($data['country']) {
            $country = \App\Models\Country::findOrFail($data['country']);
            $field = '+' . $country->phonecode . $field;
            $mode = 'phone';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'newPassword' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            $jsonResponse->setValidator($validator);
            $jsonResponse->setStatusMessage($validator->errors()->first());
            $jsonResponse->setHttpCode(400);
            return $jsonResponse->render();
        }

        if ($mode === 'email') {
            $reset = \App\Models\PasswordReset::where(['field' => $field])->first();
            if ($reset && $reset->token == $data['code']) {

                DB::beginTransaction();
                try {
                    $newPassword = str_random(6); //not for now
                    $user = $reset->user;
                    $user->password = Hash::make($request->get('newPassword'));
                    $user->save();

                    if ($reset) {
                        /* if ($mode == 'email') {
                          $mail = new \App\Mail\PasswordEmail($user, $newPassword);
                          Mail::to($user)->send($mail);
                          } else {

                          $smsResult = \App\Lib\SMSManager::getInstance()->sendSMS($user->completePhone(), __('auth.password_sms', ['name' => $user->name, 'password' => $newPassword, 'app' => config('app.name')]));
                          } */ }
                    DB::commit();
                    //$jsonResponse->setStatusMessage(__('auth.reset_password_by_' . $mode));
                    $jsonResponse->setStatusMessage(__('auth.password_set'));
                } catch (\Exception $ex) {
                    DB::rollback();
                    $jsonResponse->setSuccess(false);
                    $jsonResponse->setHttpCode(500);
                    $jsonResponse->setStatusMessage($ex->getMessage());
                } finally { }
            } else {
                $jsonResponse->setSuccess(false);
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setStatusMessage(__('auth.reset_code_not_found'));
            }
        } else {
            $user = User::where(['country_id' => $data['country'], 'phone' => $data['field']])->first();
            if ($user) {

                DB::beginTransaction();
                try {
                    $newPassword = str_random(6); //not for now
                    $user->password = Hash::make($request->get('newPassword'));
                    $user->save();
                    DB::commit();
                    $jsonResponse->setStatusMessage(__('auth.password_set'));
                } catch (\Exception $ex) {
                    DB::rollback();
                    $jsonResponse->setSuccess(false);
                    $jsonResponse->setHttpCode(500);
                    $jsonResponse->setStatusMessage($ex->getMessage());
                } finally { }
            } else {
                $jsonResponse->setSuccess(false);
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setStatusMessage(__('auth.reset_code_not_found'));
            }
        }


        return $this->renderResponse();
    }

    protected function getGuestUser()
    {
        $user = new User([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'name' => 'Guest',
        ]);
        $user->role = 'GUEST';
        return $user;
    }

    protected function me(Request $request, Response $response)
    {
        $user = auth('api')->user();
        if (!$user) {
            abort(401);
        }
        $user = $user->refresh();
        $user->profile;
        $payload = (new \App\Http\Resources\UserResource($user));
        $jsonResponse = $this->getResponseInstance();
        $jsonResponse->setPayload($payload);
        return $jsonResponse->render();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $real = true)
    {




        $rules = [
            'name' => 'required|alpha_dash|max:255|min:4',
            'password' => 'required|string|min:6|confirmed',
            //'password_confirmation' => 'sameas:password',
            'gender' => 'required|string|min:4',
            'phone' => 'required_without:email|numeric|min:6',
            'dob' => 'required|date_format:Y-m-d|before:' . \Carbon\Carbon::now()->addYears(-18)->addDays(1),
            'email' => 'required_without:phone|string|email|max:255',
            'role' => 'required|in:USER,PROVIDER',
        ];

        if (isset($data['phone'])) {
            $rules['country'] = 'required|exists:countries,id';
            $rules['phone'] .= '|userUniquePhone:phone,' . \App\Models\Country::find($data['country'])->phonecode;
        } else {
            $rules['email'] .= '|unique:users';
        }

        if (isset($data['referer_code']) && $data['referer_code']) {
            // $rules['referer_code'] = 'string|min:8|exists:users,referer_code';
        }


        if (isset($data['role']) && $data['role'] == 'PROVIDER') {
            $rules['first_name'] = 'required|string|max:255|min:3';
            $rules['last_name'] = 'required|string|max:255|min:3';
            $rules['identity_id'] = 'required|string|max:255|min:5';
            $rules['identity_country'] = 'required|exists:countries,id';
            if ($real) {
                $rules['avatar'] = 'required|image|mimes:jpeg,png,jpg,gif';
            }
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $name = $data['name'];
        do {
            $user = User::where(['name' => $name])->first();
            if ($user) {
                $name = $data['name'] . '_' . str_pad(rand(0, pow(10, 4) - 1), 4, '0', STR_PAD_LEFT);
            }
        } while ($user);
        $referer_id = null;
        if (isset($data['referer_code']) && $data['referer_code']) {
            $referer = User::where(['referer_code' => $data['referer_code']])->first();
            if ($referer) {
                $referer_id = $referer->id;
            }
        }

        $user = User::create([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'name' => $name,
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'phone_verified' => isset($data['phone']) ? true : false,
            'email' => isset($data['email']) ? $data['email'] : null,
            'role' => $data['role'],
            'country_id' => isset($data['country']) ? $data['country'] : null,
            'email_token' => str_random(6),
            'phone_token' => str_pad(rand(0, pow(10, 4) - 1), 4, '0', STR_PAD_LEFT),
            'password' => Hash::make($data['password']),
            'dob' => $data['dob'],
            'gender' => $data['gender'],
            'referer_code' => \App\Lib\Utils::generate_unique_code(8),
            'referer_id' => $referer_id
        ]);


        if ($data['role'] === 'PROVIDER') {
            $file = request()->file('avatar', null);
            if ($file) {
                $user->saveImage($file);
                $user->save();
            }

            $profile = $user->profile;
            $profile->save();
            $profile->first_name = $data['first_name'];
            $profile->last_name = $data['last_name'];
            $profile->identity_id = $data['identity_id'];
            $profile->country_id = $data['identity_country'];
            $profile->save();
        }

        $user->refresh();
        return $user;
    }
}
