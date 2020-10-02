<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Auth::guard()->user() && Auth::guard()->user()->role == 'ADMIN') {
            return $next($request);
        } else {
            Auth::guard()->logout();
        }


        if ($request->wantsJson()) {
            $text = trans('messages.auth.session-invalid');
            $response = new \App\Lib\Api\JSONResponse();
            $response->setHttpCode(401);
            $response->setStatusMessage($text);
            return $response->render();
        } else {
            return redirect(route('admin.login'));
        }
    }

}
