<?php

namespace App\Http\Controllers\Admin;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * Description of DashboardController
 *
 * @author pablo
 */
class DashboardController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {



        return view('admin.dashboard');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Auth::guard()->logout();

        $request->session()->invalidate();

        return redirect(route('admin.login'));
    }

}
