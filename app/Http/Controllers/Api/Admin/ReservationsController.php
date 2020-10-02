<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Description of ReservationsController
 *
 * @author pablo
 */
class ReservationsController extends \App\Http\Controllers\Api\BaseController
{

    public function index(Request $request)
    {
        $where = [];

        $user = null;
        if ($request->get('user')) {
            $user = User::findOrFail($request->get('user'));
        }

        $query = $request->get('query', null);


        $order = $request->get('order', 'date_from');
        $dir = 'DESC';
        if (strpos($order, '-') === 0) {
            $dir = 'DESC';
            $order = substr($order, 1);
        }

        if ($user) {
            if ($user->role == 'USER') {
                $where['appointments.customer_id'] = $user->id;
            } else if ($user->role == 'PROVIDER') {
                $where['appointments.provider_id'] = $user->id;
            }
        }


        $records = Appointment::with(['provider', 'customer', 'history'])
            ->where($where)->orderBy($order, $dir);

        if ($request->get('date_from')) {
            $records->whereRaw('DATE(date_from)>=\'' . $request->get('date_from') . "'");
        }

        if ($request->get('date_to')) {
            $records->whereRaw('DATE(date_from)<=\'' . $request->get('date_to') . "'");
        }

        switch ($request->get('status')) {
            case 'active':
                $records->where(function ($q) {
                    return $q->where(['status_name' => 'ON_THE_WAY'])->orWhere(['status_name' => 'IN_PROGRESS']);
                });
                break;
            case 'pending':
                $records->where(function ($q) {
                    return $q->where(['status_name' => 'PENDING'])->orWhere(['status_name' => 'AWAITING_ACCEPTANCE']);
                });
                break;
            case 'finalized':
                $records->where(function ($q) {
                    return $q->where(['finished' => 1])->orWhere(['status_name' => 'FINALIZED'])->orWhere(['status_name' => 'CANCELLED']);
                });
                break;
            case 'unchecked':
                $records->where('checked_at', '=', '1970-01-01 00:00:01');
                break;
        }

        if ($query) {
            $records->where(function ($q) use ($query) {
                $q->whereHas('customer', function ($q0) use ($query) {
                    $q0->where('name', 'LIKE', $query . '%');
                })->orWhereHas('provider', function ($q0) use ($query) {
                    $q0->where('name', 'LIKE', $query . '%');
                });
            });
        }

        $paginator = $records->paginate($request->get('count', 20));
        $response = $this->getResponseInstance();
        $response->setPayload($paginator);
        return $this->renderResponse();
    }

    public function getUnchecked(Request $request)
    {
        $response = $this->getResponseInstance();

        $response->setPayload(Appointment::with(['provider', 'customer'])->where(['checked_at' => '1970-01-01 00:00:01'])->orderBy('updated_at', 'desc')->get());
        return $this->renderResponse();
    }

    public function show(Request $request, $id)
    {
        $model = \App\Models\Appointment::with(['provider', 'customer', 'history', 'history.user', 'providerRate', 'providerRate.author', 'customerRate', 'customerRate.author'])->findOrFail($id);
        $model->checked_at = Carbon::now();
        $model->save();
        $response = $this->getResponseInstance();

        $response->setPayload($model);
        return $this->renderResponse();
    }
}
