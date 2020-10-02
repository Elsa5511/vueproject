<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AutoMessage;
use Illuminate\Support\Facades\Cache;

/**
 * Description of AutomessagesController
 *
 * @author pablo
 */
class AutomessagesController extends \App\Http\Controllers\Api\BaseController
{
    public function index(Request $request)
    {
        $response = $this->getResponseInstance();
        $response->setPayload(AutoMessage::orderBy('position')->get());
        return $this->renderResponse();
    }

    public function show(Request $request, $id)
    {
        $autoMessage = AutoMessage::findOrFail($id);
        $response = $this->getResponseInstance();
        $response->setPayload($autoMessage);
        return $this->renderResponse();
    }

    public function store(Request $request)
    {
        $autoMessage = AutoMessage::create($request->only('position', 'send_to', 'message', 'enabled'));
        Cache::forget('automessages');
        $response = $this->getResponseInstance();
        $response->setPayload($autoMessage);
        return $this->renderResponse();
    }

    public function update(Request $request, $id)
    {
        $autoMessage = AutoMessage::findOrFail($id);
        Cache::forget('automessages');
        $autoMessage->update($request->only('position', 'send_to', 'message', 'enabled'));
        $response = $this->getResponseInstance();
        $response->setPayload($autoMessage);
        return $this->renderResponse();
    }

    public function destroy(Request $request, $id)
    {
        $autoMessage = AutoMessage::findOrFail($id);
        $autoMessage->delete();
        Cache::forget('automessages');
        $response = $this->getResponseInstance();
        $response->setPayload(['success' => true]);
        $response->setStatusMessage("Elemento eliminado correctamente");
        return $this->renderResponse();
    }
}
