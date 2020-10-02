<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;
use App\Models\User;

class UsersController extends Controller {

    public function index(\App\DataTables\Admin\UsersDataTable $dataTable) {
        /* $savedFilter = $this->getRequestSavedParams('users.index');
          if ($savedFilter) {
          return redirect()->to(route('admin.users.index', $savedFilter));
          }
          $this->saveRequestToSession('users.index'); */
        return $dataTable->render('admin.users.index');
    }

    public function show(Request $request, $id) {

        return $this->edit($request, $id);
    }

    public function edit(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'USER'])->with(['profile'])->firstOrFail();
        if (!$user->profile) {
            $user->profile()->save(new \App\Models\UserProfile());
        }
        $user->profile;
        return view('admin.users.detail')->with(['model' => $user, 'profile' => $user->profile, 'tab' => 'basic']);
    }

    public function editProfile(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'USER'])->with(['profile'])->firstOrFail();
        if (!$user->profile) {
            $user->profile()->save(new \App\Models\UserProfile());
        }
        $user->profile;
        return view('admin.users.detail')->with(['model' => $user, 'profile' => $user->profile, 'tab' => 'profile']);
    }

    public function update(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'USER'])->firstOrFail();
        $validator = User::getValidator($request->input(), $user);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }



        $user->profile()->updateOrCreate(['user_id' => $user->id], $request->input('profile', []));
        $user->update($request->input());


        if ($request->file('image')) {
            $user->saveImage($request->file('image'));
            $user->save();
        }

        return redirect()->back()->withInput()->with(['message' => 'Usuario guardado correctamente!']);
    }

    public function updateProfile(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'USER'])->firstOrFail();
        $validator = User::getValidator($request->input(), $user);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $user->profile()->updateOrCreate(['user_id' => $user->id], $request->input('profile', []));
        $user->update($request->input());
        return redirect()->back()->withInput()->with(['message' => 'Usuario guardado correctamente!']);
    }

    public function editSecurity(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'USER'])->with(['profile'])->firstOrFail();

        return view('admin.users.detail')->with(['model' => $user, 'profile' => $user->profile, 'tab' => 'security']);
    }

    public function updateSecurity(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'USER'])->with(['profile'])->firstOrFail();
        if ($request->get('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->get('password'));
        }

        if ($request->get('phone_verified')) {
            $user->phone_verified = 1;
        }
        $user->save();
        if ($request->get('password')) {
            \App\Lib\SMSManager::getInstance()->sendSMS($user->completePhone(), 'Hola ' . $user->name . ' tu nuevo password en vaceate.com es ' . $request->get('password'));
        }
        return redirect()->back()->withInput()->with(['message' => 'Usuario guardado correctamente!']);
    }

    public function listAppointments(Request $request, \App\DataTables\Admin\AppointmentsDataTable $dataTable, $id) {
        $user = User::where(['id' => $id, 'role' => 'USER'])->with(['profile'])->firstOrFail();
        return $dataTable->with(['user' => $user, 'status' => $request->get('status', 'all')])->render('admin.users.detail', ['model' => $user, 'tab' => 'appointments']);
    }

    public function showAppointment(Request $request, $id, $appointmentId) {
        $user = User::where(['id' => $id, 'role' => 'USER'])->with(['profile'])->firstOrFail();
        $appointment = \App\Models\Appointment::where(['id' => $appointmentId, 'provider_id' => $id])->firstOrFail();
        return view('admin.appointments.detail', ['model' => $appointment, 'backUrl' => route('admin.users.appointments', ['id' => $id])]);
    }

}
