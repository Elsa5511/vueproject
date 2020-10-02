<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

/**
 * Description of MapController
 *
 * @author pablo
 */
class MapsController extends Controller {

    public function showProvidersMap(Request $request) {
        $positions = [];
        $providers = \App\Models\User::where(['role' => 'PROVIDER'])->with('position')->get();
        foreach ($providers as $p) {
            if ($p->position) {
                $positions[] = [
                    'user' => $p,
                    'position' => $p->position,
                ];
            }
        }
        return view('admin.maps.providers', ['positions' => $positions]);
    }

}
