<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class CountriesController extends BaseCRUDController {

    public function __construct() {
        parent::__construct('\App\Models\Country', 'country');
        $this->defaultLimit=-1;
    }

    protected function getDefaultColumns(Request $request) {
        return '["id", "iso_02", "name", "phonecode", "register_enabled", "work_enabled"]';
    }
}