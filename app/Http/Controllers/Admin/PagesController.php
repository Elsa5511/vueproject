<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;
use App\Models\Page;

/**
 * Description of PagesController
 *
 * @author pablo
 */
class PagesController extends Controller {

    public function __construct() {
        ;
    }

    public function index(\App\DataTables\Admin\PagesDataTable $dataTable) {
        return $dataTable->render('admin.pages.index');
    }

    public function show(Request $request, $id) {
        return redirect(route('admin.pages.edit', ['id' => $id]));
    }

    public function edit(Request $request, $id) {
        $page = Page::findOrFail($id);
        return view('admin.pages.detail')->with(['model' => $page]);
    }

    public function update(Request $request, $id) {
        $page = Page::findOrFail($id);
        $validator = Page::getValidator($request->input(), $page);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $page->update($request->input());
        return redirect()->back()->withInput()->with(['message' => __('generics.data_saved_successfully')]);
    }

}
