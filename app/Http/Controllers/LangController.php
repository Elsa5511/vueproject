<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LangController extends Controller {

    public function index(Request $request) {
        $lang = $request->get('lang', app()->getLocale());

        return[
            'generics' => \Illuminate\Support\Facades\Lang::get('generics', [], $lang),
            'auth' => \Illuminate\Support\Facades\Lang::get('auth', [], $lang),
            'pagination' => \Illuminate\Support\Facades\Lang::get('pagination', [], $lang),
            'passwords' => \Illuminate\Support\Facades\Lang::get('passwords', [], $lang),
            'validation' => \Illuminate\Support\Facades\Lang::get('validation', [], $lang),
        ];
    }

}
