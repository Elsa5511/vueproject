<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api;

use App\Models\UserWorkZone;
use Illuminate\Http\Request;
use Cypretex\Chat\Facades\ChatFacade as Chat;
use App\Http\Controllers\Api\Chat\ConversationsController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Lib\SettingsManager;
use App\Models\Appointment;
use App\Models\User;
use App\Models\RefererComission;

/**
 * Description of AppointmentsController
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class AppointmentsController extends BaseController
{

    public function getAvailableCities(Request $request, $countryId = null)
    {
        if (!$countryId) {
            $countryId = config('custom.default_country_id');
        }
        $cities = UserWorkZone::select(DB::raw('DISTINCT(cities.id) as id, cities.name as name, COUNT(users.id) as providers_count'))
            ->join('users', ['user_id' => 'users.id'])
            ->where('users.status', 'ACTIVE')
            ->join('cities', ['city_id' => 'cities.id'])
            ->groupBy('id')
            ->orderBy('cities.name', 'ASC')->get();
        $response = $this->getResponseInstance();
        $response->setHttpCode(200);
        $response->setPayload($cities);
        return $this->renderResponse();
    }

    public function store(Request $request)
    {
        $fields = $request->only('customer_id', 'provider_id', 'datetime', 'hours', 'notes', 'attributes', 'currency', 'total_price');
        $response = $this->getResponseInstance();
        DB::beginTransaction();
        try {
            $provider = \App\Models\User::where(['role' => 'PROVIDER', 'id' => $request->get('provider_id')])->with('profile')->firstOrFail();
            $user = $this->getUser();
            if ($user->role === 'ADMIN') {
                $user = \App\Models\User::where(['role' => 'USER', 'id' => $request->get('customer_id')])->with('profile')->firstOrFail();
            }




            $dateFrom = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->get('date_from'));
            $dateTo = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->get('date_from'))->addHours($request->get('hours'));

            $appointment = new \App\Models\Appointment();
            $appointment->attributes = json_encode($request->get('attributes', []));
            $appointment->customer_id = $user->id;
            $appointment->provider_id = $request->get('provider_id');
            $appointment->date_from = $dateFrom;
            $appointment->date_to = $dateTo;
            $appointment->hours = $request->get('hours');
            $appointment->notes = $request->get('notes');
            $appointment->address = $request->get('address');
            $appointment->currency = $request->get('currency');

            if ($this->getUser()->role == 'ADMIN') {
                $appointment->checked_at = Carbon::now();
            }


            //calculate price
            $price = $provider->getPriceForHours($appointment->hours, $appointment->currency);
            if (!$price) {
                $response->setStatusMessage('Este usuario aún no ha colocado los precios!');
                $response->setHttpCode(400);
                return $this->renderResponse();
            }

            $isAvailable = $provider->isAvailableForDate($appointment->date_from, $appointment->date_to);
            if (!$isAvailable) {
                $response->setStatusMessage('Este usuario ya tiene una solicitud o ya no está disponible para las fechas indicadas!');
                $response->setHttpCode(400);
                return $this->renderResponse();
            }

            /* $isAvailable = $user->isAvailableForDate($appointment->date_from, $appointment->date_to);
              if (!$isAvailable) {
              $response->setStatusMessage('Ya tienes una solicitud que para esta fecha y horas, por favor cancela la solicitud anterior antes de reservar nuevamente!');
              $response->setHttpCode(400);
              return $this->renderResponse();
              } */


            $appointment->location = json_encode($request->get('location', []));
            $appointment->base_price = $price->value;
            $appointment->total_price = $price->value;
            $appointment->referer_commision = SettingsManager::getValue('referer_commision', 0.00, 'double', true) * $appointment->total_price;
            $appointment->services_price = 0.00;
            $appointment->save();
            $appointment->refresh();

            $status = new \App\Models\AppointmentHistory();
            $status->status = $appointment->status_name;
            $status->appointment_id = $appointment->id;
            $status->user_id = $user->id;
            $status->save();


            $conversation = ConversationsController::getConversationBetweenUsers($user, $provider);
            $conversation->update(['data' => ['appointment_id' => $appointment->id]]);
            $appointment->current_status_id = $status->id;
            $appointment->chat_id = $conversation->id;
            $appointment->save();



            $appointment->provider;
            $appointment->customer;


            $response->setPayload(new \App\Http\Resources\AppointmentResource($appointment));
            $response->setSuccess(true);
            $response->setStatusMessage('Cita guardada correctamente!');


            $notification = new \App\Models\Notification();
            $notification->setAction('/reservations/' . $appointment->id);
            $notification->setIcon($user->small_avatar_url);
            $notification->setTitle('Nueva solicitud de cita');
            $notification->setType('APPOINTMENT_CREATED');
            $notification->setMessage($user->name . " ha solicitado una cita!");
            $notification->setSenderId($user->id);
            //$notification->addAttribute('appointment', $appointment->toArray());
            $notification->addAttribute('appointment_id', $appointment->id);

            if (config('custom.send_appointment_sms', true)) {
                try {
                    \App\Lib\SMSManager::getInstance()->sendSMS($provider->completePhone(), $user->name . " ha solicitado una cita! Detalles " . config('app.frontend_url') . '/reservations/' . $appointment->id);
                } catch (\Exception $ex) { }
            }

            $provider->notify(new \App\Notifications\GenericNotification($provider, $notification, 'APPOINTMENT_CREATED'));
            $user->notify(new \App\Notifications\GenericNotification($user, $notification, 'APPOINTMENT_CREATED', ['broadcast']));


            $notification->setDestType('mobile');
            $provider->notify(new \App\Notifications\GenericNotification($provider, $notification, 'APPOINTMENT_CREATED', ['fcm']));



            DB::commit();

            event(new \App\Events\AdminEvent('RESERVATION_ADDED', $appointment));

            return $this->renderResponse();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    private function saveComission(Appointment $appointment)
    {
        $user = $appointment->customer;
        $provider = $appointment->provider;

        if ($user->referer_id) {
            $userReferer = User::where(['id' => $user->referer_id])->first();
            if ($userReferer) {
                RefererComission::create([
                    'user_id' => $user->referer_id,
                    'refered_id' => $user->id,
                    'appointment_id' => $appointment->id,
                    'currency' => $appointment->currency,
                    'amount' => $appointment->referer_commision,
                    'status' => 'DEBIDO'
                ]);
            }
        }

        if ($provider->referer_id) {
            $providerReferer = User::where(['id' => $provider->referer_id])->first();
            if ($providerReferer) {
                RefererComission::create([
                    'user_id' => $provider->referer_id,
                    'refered_id' => $provider->id,
                    'appointment_id' => $appointment->id,
                    'currency' => $appointment->currency,
                    'amount' => $appointment->referer_commision,
                    'status' => 'DEBIDO'
                ]);
            }
        }
    }

    public function update(Request $request, $appointmentId)
    {
        DB::beginTransaction();

        $response = $this->getResponseInstance();
        try {
            $appointment = \App\Models\Appointment::findOrFail($appointmentId);
            $customer = $appointment->customer;
            $provider = $appointment->provider;

            $user = $this->getUser();
            if ($user->role != 'ADMIN') {
                if ($appointment->customer_id != $user->id && $appointment->provider_id != $user->id) {
                    abort(401);
                }
            }
            $fields = $request->only('datetime', 'hours', 'notes', 'attributes');

            if ($appointment->finished) {
                $response->setHttpCode(400);
                $response->setStatusMessage('No puedes cambiar una solicitud que ya está finalizada!');
            }

            $reqStatus = $request->get('status_name');
            $newStatus = $appointment->status_name;
            $actionText = "ha actualizado la solicitud!";


            switch ($reqStatus) {
                case 'PENDING':
                    $newStatus = 'PENDING';
                    $actionText = " ha aceptado la solicitud!";
                    break;
                case 'IN_PROGRESS':
                    $newStatus = 'IN_PROGRESS';
                    if (config('app.mode') === 'live') {
                        $actionText = " ha comenzado el servicio! El tiempo del servicio empieza a correr, favor realizar su pago ahora, y esperamos que la pase súper bien";
                    } else {
                        $actionText = " ha comenzado el servicio!";
                    }
                    break;
                case 'DONE':
                    if ($appointment->status->status == 'DONE') {
                        $newStatus = 'DONE';
                        if (config('app.mode') === 'live') {
                            $actionText = " ha finalizado el servicio! Ahora puedes calificar a tu modelo!";
                        } else {
                            $actionText = " ha finalizado el servicio! Recuerde calificar al profesional!";
                        }
                    }
                    break;
                case 'CANCELLED':
                    $actionText = " ha cancelado la solicitud!";
                    $newStatus = 'CANCELLED';
                    break;
                case 'FINALIZED':
                    //check for commisions
                    $this->saveComission($appointment);
                    break;
                case 'ON_THE_WAY':
                    $newStatus = 'ON_THE_WAY';
                    if (config('app.mode') === 'live') {
                        $actionText = " está en camino! favor estar pendiente, puede rastrear su ubicación en tiempo real";
                    } else {
                        $actionText = " está en camino! favor estar pendiente, puede rastrear su ubicación en tiempo real";
                    }

                    break;
            }





            $dateFrom = \Carbon\Carbon::createFromFormat('Y/m/d H:i:s', $request->get('date_from'));
            $dateTo = \Carbon\Carbon::createFromFormat('Y/m/d H:i:s', $request->get('date_from'))->addHours($request->get('hours'));

            if ($newStatus === 'PENDING') {
                $isAvailable = $customer->isAvailableForDate($appointment->date_from, $appointment->date_to);
                if (!$isAvailable) {
                    $response->setStatusMessage('Este usuario ya tiene una solicitud o ya no está disponible para las fechas indicadas!');
                    $response->setHttpCode(400);
                    return $this->renderResponse();
                }

                $isAvailable = $provider->isAvailableForDate($appointment->date_from, $appointment->date_to);
                if (!$isAvailable) {
                    $response->setStatusMessage('Ya tienes una solicitud que para esta fecha y horas, por favor cancela la solicitud anterior antes de reservar nuevamente!');
                    $response->setHttpCode(400);
                    return $this->renderResponse();
                }
            }

            if ($newStatus === 'ON_THE_WAY' && $this->getUser()->current_appointment_id) {
                $current = \App\Models\Appointment::find($this->getUser()->current_appointment_id);
                if (!$current || $current->finished || strtolower($current->status_name) == 'finalized') {
                    $appointment->provider->presence = 'ONLINE';
                    $appointment->provider->current_appointment_id = null;
                    event(new \App\Events\UserStatusChanged($appointment->provider));
                    $appointment->provider->save();
                } else {
                    $response->setStatusMessage('Debes finalizar la solicitud anterior para poder comenzar una nueva solicitud!');
                    $response->setHttpCode(400);
                    return $this->renderResponse();
                }
            }

            if ($newStatus === 'ON_THE_WAY' || $newStatus == 'IN_PROGRESS') {
                $appointment->provider->presence = 'OFFLINE';
                $appointment->provider->current_appointment_id = $appointment->id;
                event(new \App\Events\UserStatusChanged($appointment->provider));
                $appointment->provider->save();
            } else {
                if ($appointment->provider->current_appointment_id == $appointment->id) {
                    $appointment->provider->presence = 'ONLINE';
                    $appointment->provider->current_appointment_id = null;
                    event(new \App\Events\UserStatusChanged($appointment->provider));
                    $appointment->provider->save();
                }
            }

            //calculate price
            $price = $provider->getPriceForHours($appointment->hours, $appointment->currency);
            if (!$price) {
                $response->setStatusMessage('El precio no esta disponible!');
                $response->setHttpCode(400);
                return $this->renderResponse();
            }

            $appointment->attributes = json_encode($request->get('attributes', []));
            $appointment->date_from = $dateFrom;
            $appointment->date_to = $dateTo;
            $appointment->hours = $request->get('hours');
            $appointment->notes = $request->get('notes');
            $appointment->address = $request->get('address');
            $appointment->location = json_encode($request->get('location', []));
            $appointment->base_price = $provider->profile->hourly_rate ? $provider->profile->hourly_rate : 0;
            $appointment->total_price = $price->value;
            $appointment->currency = $request->get('currency', 'USD');
            $appointment->services_price = 0.00;
            $appointment->accepted = $request->get('accepted', false);
            $appointment->finished = $request->get('finished', false);
            if ($user->role != 'ADMIN') {
                $appointment->checked_at = Carbon::createFromFormat('Y-m-d H:i:s', '1970-01-01 00:00:01');
            } else {
                $appointment->checked_at = Carbon::now();
            }
            $appointment->save();
            $appointment->refresh();




            if ($reqStatus != $appointment->status->status) {
                $status = new \App\Models\AppointmentHistory();
                $status->status = $reqStatus;
                $status->appointment_id = $appointment->id;
                $status->user_id = $user->id;
                $status->description = trim($request->get("status_reason"));
                $status->save();
                $appointment->status_name = $reqStatus;
                $appointment->current_status_id = $status->id;
                $appointment->save();
            }





            $notification = new \App\Models\Notification();
            $notification->setAction('/reservations/' . $appointment->id);
            $notification->setIcon($this->getUser()->small_avatar_url);
            $notification->setTitle('Actualizacion de solicitud');
            $notification->setType('APPOINTMENT_UPDATED');
            $notification->setMessage($this->getUser()->name . " " . $actionText);
            $notification->setSenderId($this->getUser()->id);
            //$notification->addAttribute('appointment', $appointment->toArray());
            $notification->addAttribute('appointment_id', $appointment->id);
            if ($this->getUser()->id == $appointment->provider->id) {
                $customer->notify(new \App\Notifications\GenericNotification($customer, $notification, 'APPOINTMENT_UPDATED'));

                $provider->notify(new \App\Notifications\GenericNotification($provider, $notification, 'APPOINTMENT_UPDATED', ['broadcast']));

                $notification->setDestType('mobile');
                $customer->notify(new \App\Notifications\GenericNotification($customer, $notification, 'APPOINTMENT_UPDATED', ['fcm']));


                if (config('custom.send_appointment_sms', true) && ($reqStatus == 'IN_PROGRESS' || $reqStatus == 'PENDING' || $reqStatus == 'ON_THE_WAY')) {
                    //notify provider by sms
                    try {
                        \App\Lib\SMSManager::getInstance()->sendSMS($customer->completePhone(), $this->getUser()->name . " " . $actionText . ". Detalles " . config('app.frontend_url') . '/reservations/' . $appointment->id);
                        if ($reqStatus == 'IN_PROGRESS') {
                            \App\Lib\SMSManager::getInstance()->sendSMS($provider->completePhone(), "El tiempo de servicio a comenzado, favor dar lo mejor de ti, si el cliente no le paga por adelantado llamenos...");
                        }
                    } catch (\Exception $ex) { }
                }
            } else {
                $provider->notify(new \App\Notifications\GenericNotification($provider, $notification, 'APPOINTMENT_UPDATED'));
                $customer->notify(new \App\Notifications\GenericNotification($customer, $notification, 'APPOINTMENT_UPDATED', ['broadcast']));

                $notification->setDestType('mobile');
                $provider->notify(new \App\Notifications\GenericNotification($provider, $notification, 'APPOINTMENT_UPDATED', ['fcm']));
                if (config('custom.send_appointment_sms', true) && $reqStatus != 'CANCELLED') {
                    //notify provider by sms
                    try {
                        \App\Lib\SMSManager::getInstance()->sendSMS($provider->completePhone(), "Hola " . $provider->name . ", " . $user->name . " " . $actionText . ". Detalles " . config('app.frontend_url') . '/reservations/' . $appointment->id);
                    } catch (\Exception $ex) { }
                }
            }
            $appointment->history;
            $response->setPayload(new \App\Http\Resources\AppointmentResource($appointment));
            DB::commit();

            event(new \App\Events\AdminEvent('RESERVATION_UPDATED', $appointment));

            return $this->renderResponse();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public function index(Request $request)
    {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            if ($request->get('user_id')) {
                $user = User::findOrFail($request->get('user_id'));
            }
        } else {
            $user = $this->getUser();
        }

        $statuses = $request->get('statuses', []);
        $status = $request->get('status', 'pending');

        $appointments = \App\Models\Appointment::select('*')->with('customer', 'provider', 'status');
        if ($user) {
            if ($user->role == 'PROVIDER') {
                $appointments->where('provider_id', '=', $user->id);
            } elseif ($user->role == 'USER') {
                $appointments->where('customer_id', '=', $user->id);
            } else {
                abort(401);
            }
        }

        if (count($statuses)) {
            $appointments->whereIn('status_name', $statuses);
        }
        if ($status) {
            switch ($status) {
                case 'pending':
                    $appointments->where('status_name', '=', 'awaiting_acceptance')->orderBy('date_from', 'ASC');
                    break;
                case 'upcoming':
                    $field = $user->role === 'USER' ? 'provider_rated' : 'customer_rated';
                    $appointments->where('finished', '=', 0)->where('status_name', '!=', 'finalized')->where('status_name', '!=', 'awaiting_acceptance')->orderBy('date_from', 'ASC');
                    break;
                case 'rate_pending':
                    $field = $user->role === 'USER' ? 'provider_rated' : 'customer_rated';
                    $appointments->where('finished', '=', 0)->where('status_name', '=', 'finalized')->where($field, '=', 0)->orderBy('date_from', 'DESC');
                    break;
                case 'done':
                    $field = $user->role === 'USER' ? 'provider_rated' : 'customer_rated';
                    if ($user->role === 'USER') {
                        $appointments->where('date_to', '>=', \Illuminate\Support\Carbon::now()->addDays(-1));
                    }
                    $appointments->where(function ($q) use ($field) {
                        $q->where('finished', '=', 1)->orWhere($field, '=', 1);
                    })->orderBy('date_from', 'DESC');
                    break;
                case 'unchecked':
                    $appointments->where('checked_at', '=', '1970-01-01 00:00:01');
                    break;
            }
        }

        /* $appointments->where(function($q) use ($user) {
          $q->where(['provider_id' => $user->id])->orWhere(['customer_id' => $user->id]);
          }); */


        //DB::connection()->enableQueryLog();
        $response = $this->getResponseInstance();
        $response->setPayload($appointments->paginate(50));
        /* $queries = DB::getQueryLog();
          $response->setPayload($queries); */
        return $this->renderResponse();
    }

    public function stats(Request $request)
    {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            if ($request->get('user_id')) {
                $user = User::findOrFail($request->get('user_id'));
            }
        } else {
            $user = $this->getUser();
        }

        $stats = [];

        foreach (['pending', 'upcoming', 'rate_pending', 'done', 'unchecked'] as $status) {

            $appointments = \App\Models\Appointment::select('*')->with('customer', 'provider', 'status');
            if ($user) {
                if ($user->role == 'PROVIDER') {
                    $appointments->where('provider_id', '=', $user->id);
                } elseif ($user->role == 'USER') {
                    $appointments->where('customer_id', '=', $user->id);
                } else {
                    abort(401);
                }
            }
            switch ($status) {
                case 'pending':
                    $appointments->where('status_name', '=', 'awaiting_acceptance')->orderBy('date_from', 'ASC');
                    break;
                case 'upcoming':
                    $field = $user->role === 'USER' ? 'provider_rated' : 'customer_rated';
                    $appointments->where('finished', '=', 0)->where('status_name', '!=', 'finalized')->where('status_name', '!=', 'awaiting_acceptance')->orderBy('date_from', 'ASC');
                    break;
                case 'rate_pending':
                    $field = $user->role === 'USER' ? 'provider_rated' : 'customer_rated';
                    $appointments->where('finished', '=', 0)->where('status_name', '=', 'finalized')->where($field, '=', 0)->orderBy('date_from', 'DESC');
                    break;
                case 'done':
                    $field = $user->role === 'USER' ? 'provider_rated' : 'customer_rated';
                    $appointments->where(function ($q) use ($field) {
                        $q->where('finished', '=', 1)->orWhere($field, '=', 1);
                    })->orderBy('date_from', 'DESC');
                    break;
                case 'unchecked':
                    $appointments->where('checked_at', '=', '1970-01-01 00:00:01');
                    break;
            }

            $stats[$status] = $appointments->count();
        }

        /* $appointments->where(function($q) use ($user) {
          $q->where(['provider_id' => $user->id])->orWhere(['customer_id' => $user->id]);
          }); */


        //DB::connection()->enableQueryLog();
        $response = $this->getResponseInstance();
        $response->setPayload($stats);
        /* $queries = DB::getQueryLog();
          $response->setPayload($queries); */
        return $this->renderResponse();
    }

    public function show(Request $request, $appointmentId)
    {
        $appointment = \App\Models\Appointment::findOrFail($appointmentId);
        $user = $this->getUser();
        if (!$user) {
            abort(401);
        }
        if ($user->role != 'ADMIN') {
            if ($appointment->customer_id != $user->id && $appointment->provider_id != $user->id) {
                abort(401);
            }
        } else {
            $appointment->checked_at = Carbon::now();
            $appointment->save();
        }
        $otherUser = null;
        $appointment->customer;
        $appointment->provider;
        $appointment->history;
        if ((!$appointment->provider_rated) || (!$appointment->customer_rated)) {
            if ($user->role == 'USER') {
                $appointment->providerRate;
            } else {
                $appointment->customerRate;
            }
        } else {
            $appointment->providerRate;
            $appointment->customerRate;
        }

        if ($user->role == 'USER') {
            $otherUser = $appointment->provider;
        } else {
            $otherUser = $appointment->customer;
        }



        $response = $this->getResponseInstance();
        $payload = (new \App\Http\Resources\AppointmentResource($appointment));
        $payload->actions = [
            'test' => true,
            'call' => $user->can('make-call', $otherUser),
            'chat' => $user->can('init-conversation', $otherUser),
            'reserve' => $user->can('make-reservation', $otherUser),
            'position' => $user->can('watch-position', $appointment),
            'profile' => $user->can('view-profile', $otherUser),
        ];
        $response->setPayload($payload);
        return $this->renderResponse();
    }

    public function rate(Request $request, $appointmentId)
    {
        $appointment = \App\Models\Appointment::findOrFail($appointmentId);
        $user = $this->getUser();
        if ($user->role != 'ADMIN') {
            if ($appointment->customer_id != $user->id && $appointment->provider_id != $user->id) {
                abort(401);
            }
        }
        $appointment->customer;
        $appointment->provider;
        $appointment->history;

        $rate = $request->get('rate', null);
        $comment = $request->get('review', null);
        $role = $user->role;

        if ($role == 'CUSTOMER' && $appointment->provider_rated) {
            abort(400);
        } elseif ($role == 'PROVIDER' && $appointment->customer_rated) {
            abort(400);
        }

        DB::beginTransaction();
        try {
            $userRate = new \App\Models\UserRate();
            $userRate->appointment_id = $appointment->id;
            $userRate->user_id = ($role == 'PROVIDER') ? $appointment->customer_id : $appointment->provider_id;
            $userRate->user_role = ($role == 'PROVIDER') ? 'CUSTOMER' : 'PROVIDER';
            $userRate->rate = $rate;
            $userRate->review = $comment;
            $userRate->author_id = $user->id;
            $userRate->save();

            if ($role == 'USER') {
                $appointment->provider_rated = true;
            } else {
                $appointment->customer_rated = true;
            }

            if ($appointment->provider_rated && $appointment->customer_rated) {
                $appointment->finished = true;
            }

            $appointment->save();

            $userRate->user;
            $userRate->author;
            $userRate->appointment;

            $ratedUser = $userRate->user;
            $result = DB::table('user_rates')
                ->select(DB::raw('SUM(rate)/COUNT(id) as rate'))
                ->where(['user_id' => $userRate->user->id])
                ->first();
            $ratedUser->rate = $result->rate;
            $ratedUser->save();



            $notification = new \App\Models\Notification();
            $notification->setAction('/reservations/' . $appointment->id);
            $notification->setIcon($this->getUser()->small_avatar_url);
            $notification->setTitle('Actualizacion de solicitud');
            $notification->setType('APPOINTMENT_UPDATED');
            $notification->setMessage($this->getUser()->name . " te ha calificado!");
            $notification->setSenderId($this->getUser()->id);
            //$notification->addAttribute('appointment', $appointment->toArray());
            $notification->addAttribute('appointment_id', $appointment->id);


            if ($user->id == $appointment->provider->id) {
                $appointment->customer->notify(new \App\Notifications\GenericNotification($appointment->customer, $notification, 'APPOINTMENT_UPDATED'));
                $appointment->provider->notify(new \App\Notifications\GenericNotification($appointment->provider, $notification, 'APPOINTMENT_UPDATED', ['broadcast']));
                $notification->setDestType('mobile');
                $appointment->customer->notify(new \App\Notifications\GenericNotification($appointment->customer, $notification, 'APPOINTMENT_UPDATED', ['fcm']));
            } else {
                $appointment->provider->notify(new \App\Notifications\GenericNotification($appointment->provider, $notification, 'APPOINTMENT_UPDATED'));
                $appointment->customer->notify(new \App\Notifications\GenericNotification($appointment->customer, $notification, 'APPOINTMENT_UPDATED', ['broadcast']));
                $notification->setDestType('mobile');
                $appointment->provider->notify(new \App\Notifications\GenericNotification($appointment->provider, $notification, 'APPOINTMENT_UPDATED', ['fcm']));
            }

            DB::commit();

            event(new \App\Events\AdminEvent('RESERVATION_UPDATED', $appointment));
            $response = $this->getResponseInstance();
            $response->setPayload($userRate);
            return $this->renderResponse();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public function getUserPosition(Request $request, $appointmentId)
    { }

    public function getPendingAppointments(Request $request, $otherUserId)
    {
        $user = $this->getUser();
        $otherUser = \App\Models\User::findOrFail($otherUserId);

        $response = $this->getResponseInstance();
        $appointments = \App\Models\Appointment::where(['finished' => false])->where(function ($q) use ($otherUser, $user) {
            $q->where(['customer_id' => $otherUser->id, 'provider_id' => $user->id])
                ->orWhere(['customer_id' => $user->id, 'provider_id' => $otherUser->id]);
        })->where(function ($q) use ($otherUser, $user) {
            $q->where('status_name', '!=', 'FINALIZED');
        })->orderBy('date_from', 'ASC')->get();
        $response->setHttpCode(200);
        $response->setPayload($appointments);
        return $this->renderResponse();
    }
}
