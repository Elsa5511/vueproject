<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Cypretex\Chat\Facades\ChatFacade as Chat;
use Illuminate\Support\Facades\DB;

class PagesController extends BaseCRUDController {

    public function __construct() {
        parent::__construct(\App\Models\Page::class, 'page');
        $this->defaultLimit = -1;
    }

    protected function getDefaultColumns(Request $request) {
        return '["id", "title", "slug", "body"]';
    }

    public function getBySlug(Request $request, $slug) {
        $jsonResponse = $this->getResponseInstance();
        $page = \App\Models\Page::where(['slug' => $slug])->first();
        if ($page) {
            $jsonResponse->setPayload($page);
        } else {
            $jsonResponse->setHttpCode(404);
        }
        return $jsonResponse->render();
    }

}
