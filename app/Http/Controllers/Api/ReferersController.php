<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Models\RefererComission;

class ReferersController extends BaseController
{
    public function index(Request $request)
    {
        $user = $this->getUser();
        if ($user->role === 'ADMIN') {
            $user = User::findOrFail($request->get('user_id'));
        }

        $response = $this->getResponseInstance();
        $response->setPayload($user->refereds()->orderBy('name', 'asc')->get());
        return $this->renderResponse();
    }


    public function getReservationsOfRefered(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->role === 'ADMIN') {
            $user = User::findOrFail($request->get('user_id'));
        }

        $referer = User::where(['referer_id' => $user->id, 'id' => $id])->firstOrFail();
        $response = $this->getResponseInstance();
        $response->setPayload([
            'user' => $referer,
            'reservations' => RefererComission::with(['refered', 'appointment', 'appointment.history', 'appointment.customer', 'appointment.provider'])->where('refered_id', '=', $id)->where('created_at', '>', Carbon::now()->addMonths(-3))->orderBy('created_at', 'desc')->get(),
        ]);
        return $this->renderResponse();
    }
}
