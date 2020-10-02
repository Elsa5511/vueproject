<?php

namespace App\Http\Middleware;

use Closure;

class CheckToken {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {


        $token = auth('api')->getToken();

        if ($token) {
            $userToken = \App\Models\UserToken::where(['token' => $token])->first();
            if (!$userToken) {
                auth('api')->logout();
                throw new \Illuminate\Auth\AuthenticationException();
            } else {


                $data = [
                    'Agent' => $request->header('User-Agent'),
                ];
                $userToken->attributes = $data;
                $userToken->last_ip = $request->ip();
                $userToken->last_access = \Carbon\Carbon::now();
                $userToken->version = $request->header('X-Version', '0.0.0');
                $userToken->client_type = $request->header('X-Client', 'unknown');
                $userToken->save();



                $request->request->add(['user_token', $userToken]);

            }
        }
        return $next($request);
    }

}
