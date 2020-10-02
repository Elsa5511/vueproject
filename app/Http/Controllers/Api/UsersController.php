<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Resources\UserCollection;

class UsersController extends BaseCRUDController {

    public function __construct() {
        parent::__construct('\App\Models\User', 'user');
    }

    protected function getDefaultColumns(Request $request) {
        $fields = '["id", "name", "country_id", "role", "presence"]';

        if($this->userHasRole('ADMIN')){
            $fields = '["*"]';
        }

        return $fields;
    }
    
    protected function changeQuery(Request $request, $query) {
        $query->with('profile');
    }

}
