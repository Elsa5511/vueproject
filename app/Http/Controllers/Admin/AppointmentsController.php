<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;
use App\Models\User;
use Cypretex\Chat\Facades\ChatFacade as Chat;

class AppointmentsController extends Controller {

    public function index(\App\DataTables\Admin\AppointmentsDataTable $dataTable) {
        return $dataTable->render('admin.appointments.index');
    }

    public function show(Request $request, $id) {
        $model = \App\Models\Appointment::findOrFail($id);
        return view('admin.appointments.detail', ['model' => $model]);
    }

}
