<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

/**
 * Description of AdminController
 *
 * @author pablo
 */
class Controller extends \App\Http\Controllers\Controller {

    protected $user;

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function __construct() {

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            $topMessage = null;
            if (!$request->ajax()) {
                View::share('top_message', $topMessage);
                View::share('settings', \App\Lib\SettingsManager::loadSettings());
            }
            return $next($request);
        });
    }

    public function addMessage($text, $type = 'success', $title = '', $extras = null) {
        \App\Lib\MessageManager::constructMessage($text, $type, $title, $extras);
    }

    public function getResponseVars() {
        $vars = array();
    }

    public function saveRequestToSession($storeId) {
        session()->put('pages_filter.' . $storeId, request()->query());
    }

    public function getRequestSavedParams($storeId) {
        $vars = session()->get('pages_filter.' . $storeId);

        session()->forget('pages_filter.' . $storeId);

        return $vars;
    }

}
