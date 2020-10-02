<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        $stats = [];
        $pendingProviders = \App\Models\User::where(['role' => 'PROVIDER'])->where(function($q) {
                    $q->where(['status' => 'pending'])->orWhere(['identity_verified' => false]);
                })->count();
        $stats['pendingProviders'] = $pendingProviders;
        $request->attributes->add(['stats' => $stats]);
        return $next($request);
    }

}
