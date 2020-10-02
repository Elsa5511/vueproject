<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AutoMessage;
use Illuminate\Support\Facades\Cache;
use App\Models\Page;

/**
 * Description of AutomessagesController
 *
 * @author pablo
 */
class PagesController extends \App\Http\Controllers\Api\BaseController
{
    public function index(Request $request)
    {
        $response = $this->getResponseInstance();
        $response->setPayload(Page::orderBy('title')->get());
        return $this->renderResponse();
    }

    public function show(Request $request, $id)
    {
        $response = $this->getResponseInstance();
        $response->setPayload(Page::findOrFail($id));
        return $this->renderResponse();
    }

    public function update(Request $request, $id)
    {

        $response = $this->getResponseInstance();

        $page = Page::findOrFail($id);
        $validator = Page::getValidator($request->input(), $page);
        if ($validator->fails()) {
            $response->setHttpCode(400);
            $response->setValidator($validator);
        } else {
            $page->update($request->only('title', 'body', 'published'));
            $response->setPayload($page);
        }
        return $this->renderResponse();
    }
}
