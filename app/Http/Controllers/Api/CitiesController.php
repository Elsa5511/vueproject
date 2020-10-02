<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class CitiesController extends BaseCRUDController {

    public function __construct() {
        parent::__construct('\App\Models\City', 'city');
        $this->defaultLimit = -1;
    }

    protected function getDefaultColumns(Request $request) {
        return '["id", "name", "state_id"]';
    }

    /**
     * 
     * @param Request $request
     * @param type $query
     * @return type
     */
    protected function changeQuery(Request $request, $query) {
        $country_id = $request->get('state', null);
        if ($country_id > 0) {
            $query->where('state_id', '=', $country_id);
        }
        return $query;
    }

}
