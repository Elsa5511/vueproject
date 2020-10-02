<?php

namespace App\Http\Middleware;

use Closure;

class Localization {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        //sleep(5);
        
        $local = ($request->hasHeader('Accept-Language')) ? $request->header('Accept-Language') : config()->get('locale');
        $local = ($request->hasCookie('lang')) ? $request->cookie('lang') : $local;
        $local= preg_replace('#([a-zA-z]+)_([a-zA-z]+)#', '$1', $local);
        // set laravel localization
        app()->setLocale($local);
        // continue request
        return $next($request);
    }

}
