<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticateGuest {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!auth('api')->user()) {
            auth('api')->login(new User([
                'id' => \Ramsey\Uuid\Uuid::uuid4(),
                'name' => 'Guest',
                'role' => 'GUEST',
            ]));
        }
        return $next($request);
    }

}
