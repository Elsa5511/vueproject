<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatesController extends BaseCRUDController {

    public function __construct() {
        parent::__construct('\App\Models\State', 'state');
        $this->defaultLimit=-1;
    }

    protected function getDefaultColumns(Request $request) {
        return '["id", "name", "country_id"]';
    }
    
    /**
     * 
     * @param Request $request
     * @param type $query
     * @return type
     */
    protected function changeQuery(Request $request, $query) {
        $country_id=$request->get('country', null);
        if($country_id>0){
            $query->where('country_id', '=', $country_id);
        }
        return $query;
    }


}
