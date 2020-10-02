<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AutoMessage;
use Illuminate\Support\Facades\Cache;

class AutoMessagesController extends Controller {

    public function __construct() {
        ;
    }

    public function index(\App\DataTables\Admin\AutoMessagesDataTable $dataTable) {
        return $dataTable->render('admin.automessages.index');
    }

    public function show(Request $request, $id) {
        return redirect(route('admin.automessages.edit', ['id' => $id]));
    }

    public function edit(Request $request, $id) {
        $autoMessage = AutoMessage::findOrFail($id);
        return view('admin.automessages.detail')->with(['model' => $autoMessage]);
    }

    public function create(Request $request) {
        return view('admin.automessages.create')->with(['model' => new AutoMessage()]);
    }

    public function store(Request $request) {
        //$autoMessage = ;
        /* if ($validator->fails()) {
          return redirect()->back()->withInput()->withErrors($validator);
          } */
        AutoMessage::create($request->input());
        Cache::forget('automessages');
        return redirect(route('admin.automessages.index'))->with(['message' => __('generics.data_saved_successfully')]);
    }

    public function update(Request $request, $id) {

        $autoMessage = AutoMessage::findOrFail($id);
        Cache::forget('automessages');
        $autoMessage->update($request->input());
        return redirect()->back()->withInput()->with(['message' => __('generics.data_saved_successfully')]);
    }

    public function destroy(Request $request, $id) {
        $autoMessage = AutoMessage::findOrFail($id);
        $autoMessage->delete();
        Cache::forget('automessages');
        return (new \App\Lib\Api\JSONResponse())->render();
    }

}
