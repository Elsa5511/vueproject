<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller {

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm() {

        return view('admin.auth.login');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(\Illuminate\Http\Request $request) {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    protected function authenticated(\Illuminate\Http\Request $request, $user) {
        $token = auth('api')->login($user);
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
        session()->put('user_token', $token); //Store token in the session.
    }

}
