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
use App\Models\RefererComission;

/**
 * Description of AutomessagesController
 *
 * @author pablo
 */
class ReferersController extends \App\Http\Controllers\Api\BaseController
{

    public function index(Request $request)
    {
        $query = $request->get('query', null);
        $status = $request->get('status', null);

        $records = RefererComission::with(['refered', 'user', 'appointment', 'appointment.history', 'appointment.customer', 'appointment.provider'])->orderBy('created_at', 'DESC');

        /*$records = Appointment::with(['provider', 'customer', 'history'])
            ->where('referer_commision', '>', 0)
            ->where('referer_commision_status', '=', 'DEBIDO')
            ->where('status_name', '=', 'finalized')
            ->where('date_from', '>', Carbon::now()->addMonths(-3))->orderBy('date_From');*/

        if ($status) {
            $records->where('status', '=', $status);
        }

        if ($query) {
            $records->where(function ($q) use ($query) {
                $q->whereHas('user', function ($q0) use ($query) {
                    $q0->where('name', 'LIKE', $query . '%');
                });
            });
        }

        $paginator = $records->paginate($request->get('count', 20));
        $response = $this->getResponseInstance();
        $response->setPayload($paginator);
        return $this->renderResponse();
    }

    /*public function getDone(Request $request)
    {
        $query = $request->get('query', null);

        $records = Appointment::with(['provider', 'customer', 'history'])
            ->where('referer_commision', '>', 0)->where('referer_commision_status', '=', 'PAGADO')->orderBy('date_From');

        if ($query) {
            $records->where(function ($q) use ($query) {
                $q->whereHas('customer', function ($q0) use ($query) {
                    $q0->where('name', 'LIKE', $query . '%');
                })->orWhereHas('provider', function ($q0) use ($query) {
                    $q0->wh ere('name', 'LIKE', $query . '%');
                });
            });
        }

        $paginator = $records->paginate($request->get('count', 20));
        $response = $this->getResponseInstance();
        $response->setPayload($paginator);
        return $this->renderResponse();
    }*/

    public function updatePayment(Request $request, $id)
    {
        $response = $this->getResponseInstance();
        //$model = \App\Models\Appointment::with(['provider', 'customer', 'history', 'history.user', 'providerRate', 'providerRate.author', 'customerRate', 'customerRate.author'])->findOrFail($id);
        //$model->referer_commision_status = $request->get('status');
        $model = RefererComission::findOrFail($id);
        $model->status = $request->get('status');
        if ($model->status === 'PAGADO') {
            $model->paid_on = Carbon::now();
        }
        $model->save();
        $response->setPayload(['success' => true]);
        $response->setStatusMessage('Status cambiado exitosamente');
        return $this->renderResponse();
    }
}
