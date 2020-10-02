<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;

class ProvidersController extends BaseController {

    private $defaultPageSize = 500;

    public function getPricesOptions(Request $request) {
        $response = $this->getResponseInstance();
        $response->setPayload(config('custom.price_options'));
        return $this->renderResponse();
    }

    public function index(Request $request) {
        $pageSize = $this->defaultPageSize;

        $response = $this->getResponseInstance();

        $filter = ['status' => $request->get('status', 'ACTIVE'), 'role' => 'PROVIDER', 'identity_verified' => 1];


        if (!$this->getUser()) {
            abort(403);
        }

        if ($this->getUser()->role == 'ADMIN') {
            return User::with('profile')->where(['role' => 'PROVIDER'])->paginate($request->get('count', 20));
        } else {
            if (!$this->getRequestLatitude() || !$this->getRequestLongitude()) {
                $response->setHttpCode(400);
                $response->setStatusMessage('Debe activar la ubicacion para poder ver nuestr@s chic@s!');
                return $response->render();
            }
        }


        /* $results = User::where($filter)->with(['profile', 'position'])->where('distance', function($query) use ($request){
          \App\Models\Position::select('distance')->distance($this->getRequestLatitude(), $this->getRequestLongitude())->where('user_id', 'id')->get();
          })->having('distance', '>', '0')->get(); */
        $query = User::with('profile')->where($filter)->where(function($q) use ($request) {
            $q->whereHas('position', function($q) use ($request) {
                $q->distance($this->getRequestLatitude(), $this->getRequestLongitude())->orderBy('distance', 'DESC');
            })->orderBy('distance', 'ASC');
        });

        if ($request->get('apply_distance', false) == 'true') {
            $query->whereHas('position', function($q) use ($request) {
                $q->geofence($this->getRequestLatitude(), $this->getRequestLongitude(), $request->get('distance_from', 1) - 1, $request->get('distance_to', 100));
            });
        }
        if ($request->get('country', 'all') != 'all') {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('country_id', '=', $request->get('country'));
            });
        }



        $page = $query->paginate($pageSize);

        $page->getCollection()->transform(function ($value) use ($request) {
            $value->position = $value->position()->distance($this->getRequestLatitude(), $this->getRequestLongitude())->first();

            return $value;
        });


        $payload = \App\Http\Resources\UserResource::collection($page)->transform(function($value) use ($request) {
            if ($this->isUserInBlockedZone($value)) {
                return;
            }
            $value->prices = $value->getPrices($request->get('currency', 'USD'));

            $oneHourPrice = $value->getPriceForHours(1, $request->get('currency', 'USD'));

            if ($request->get('price_from', 0) * 100 > $oneHourPrice->value && $oneHourPrice->value > 0) {
                return;
            }
            if ($request->get('price_to', 0) * 100 < $oneHourPrice->value && $oneHourPrice->value > 0) {
                return;
            }
            return $value;
        });


        $page->data = $payload;

        $response->setPayload($page);
        return $this->renderResponse();

        $page = User::where($filter)->with(['profile', 'position'])->paginate($pageSize);

        $payload = \App\Http\Resources\UserResource::collection($page);
        $page->data = $payload;
        $response->setPayload($page);
        return $this->renderResponse();
    }

    private function isHourAvailable(Request $request, $appointments) {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $request->get('date'));
        $currentDate = \Carbon\Carbon::createFromDate();
        if ($date->diffInHours($currentDate) < 1) {
            return false;
        }
        return true;
    }

    public function getAvailabilityForDate2($provider, $date, $now) {
        $hours = config('custom.hours_intervals');
        $ret = [];

        $offset = $now->diffInSeconds(\Carbon\Carbon::now(), false);

        $dateFrom = \Carbon\Carbon::createFromTimestamp($date->getTimestamp())->setTime(0, 0, 0)->addSeconds($offset);
        $dateTo = \Carbon\Carbon::createFromTimestamp($date->getTimestamp())->setTime(23, 59, 59)->addSeconds($offset)->addDay(1);


        $appointments = \App\Models\Appointment::where(function($q) use($dateFrom, $dateTo) {
                    $q->whereBetween('date_from', [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')])->orWhereBetween('date_to', [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')]);
                })->where('accepted', '=', '1')->where('finished', '=', '0')->where(function($q) use ($provider) {
                    $q->where('provider_id', '=', $provider->id);
                })->get()->all();
        $minHoursBetweenAppointments = \App\Lib\SettingsManager::getValue('appointments_min_interval', 1, 'float', true);
        $minHoursBeforeAppointments = \App\Lib\SettingsManager::getValue('appointments_min_anticipation', 1, 'float', true);
        $minTimeAmount = config('custom.min_reservation_hours');



        foreach ($appointments as $appointment) {
            $appointment->date_from = $appointment->date_from->addSeconds(-$offset)->addHours(-$minHoursBetweenAppointments);
            $appointment->date_to = $appointment->date_to->addSeconds(-$offset)->addHours($minHoursBetweenAppointments);
        }

        foreach ($hours as $hour) {
            $date->setTime(explode(':', $hour)[0], explode(':', $hour)[1], 0);
            $available = ($now->diffInMinutes($date, false)) >= $minHoursBeforeAppointments * 60 ? 'available' : 'invalid';
            if ($available === 'available') {
                foreach ($appointments as $appointment) {
                    if ($available == 'available') {
                        $available = (!$date->between($appointment->date_from, $appointment->date_to, false)) ? 'available' : 'occupied';
                        if ($available == 'available') {
                            $available = $date->diffInMinutes(\Carbon\Carbon::createFromTimestamp($appointment->date_from->getTimestamp())) >= $minTimeAmount * 60 ? 'available' : 'invalid_offset';
                        }
                    }
                }
            }
            $ret[$hour] = $available;
        }
        return $ret;
    }

    public function getAvailablity(Request $request, $providerId) {
        $provider = \App\Models\User::where(['role' => 'PROVIDER', 'id' => $providerId])->with('profile')->firstOrFail();
        //$now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $request->get('localtime', \Carbon\Carbon::now()->format('Y-m-d H:i:s')));
        $date = \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('date'), 'UTC');
        $availability = [];
        $date->addDay(-2);
        $times = $provider->getAvailabilityForDate($date);
        foreach ($times as $time => $status) {
            $availability[$date->format('Y-m-d') . ' ' . $time] = $status;
        }
        $date->addDay();
        $times = $provider->getAvailabilityForDate($date);
        foreach ($times as $time => $status) {
            $availability[$date->format('Y-m-d') . ' ' . $time] = $status;
        }
        $date->addDay();
        $times = $provider->getAvailabilityForDate($date);
        foreach ($times as $time => $status) {
            $availability[$date->format('Y-m-d') . ' ' . $time] = $status;
        }
        $date->addDay();
        $times = $provider->getAvailabilityForDate($date);
        foreach ($times as $time => $status) {
            $availability[$date->format('Y-m-d') . ' ' . $time] = $status;
        }
        $date->addDay();
        $times = $provider->getAvailabilityForDate($date);
        foreach ($times as $time => $status) {
            $availability[$date->format('Y-m-d') . ' ' . $time] = $status;
        }
        $response = $this->getResponseInstance();
        $response->setPayload([
            'availability' => $availability,
        ]);
        return $this->renderResponse();
    }

}
