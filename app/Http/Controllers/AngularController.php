<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AngularController extends Controller {

    public function serve(Request $request) {
        return File::get(public_path('apps/frontend/index.html'));
        //return view('welcome');
    }

}
