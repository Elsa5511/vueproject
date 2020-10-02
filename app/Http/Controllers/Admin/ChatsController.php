<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;
use App\Models\User;
use Cypretex\Chat\Facades\ChatFacade as Chat;

class ChatsController extends Controller {

    public function index(\App\DataTables\Admin\ChatsDataTable $dataTable) {
        return $dataTable->render('admin.chats.index');
    }

    public function destroy(Request $request, $id) {
        \Cypretex\Chat\Models\Conversation::findOrFail($id)->delete();
        return (new \App\Lib\Api\JSONResponse())->render();
    }

    public function show(Request $request, $id) {
        $conversation = Chat::conversations()->getById($id);
        return view('admin.chats.show', ['model' => $conversation]);
    }

}
