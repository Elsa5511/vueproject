<?php

namespace App\Http\Controllers\Api;

use \Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserProfileCollection;

class ProfilesController extends BaseController {

    public function update(Request $request, $userId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }

        $profile = $user->profile;
        $profile->save();
        if (!$profile) {
            abort(404);
        }
        $jsonResponse = $this->getResponseInstance();
        $profile_data = $request->only(
                'first_name', 'gender', 'last_name', 'contact_email', 'identity_id', 'dob', 'country_id', 'gender', 'city_id', 'address', 'state', 'postal_code', 'sexual_orientation', 'eyes_color', 'corporal_complexion', 'corporal_dimensions', 'height', 'weight', 'hair_color', 'skin_color'
        );

        if ($user->role === 'PROVIDER') {
            $validator = \Illuminate\Support\Facades\Validator::make($profile_data, [
                        'first_name' => 'required|string|min:4',
                        'last_name' => 'required|string|min:4',
                        'sexual_orientation' => 'required|string',
                        'eyes_color' => 'required|string',
                        'corporal_complexion' => 'required|string',
                        'hair_color' => 'required|string',
                        'skin_color' => 'required|string',
                        'height' => 'required|numeric|min:130',
                        'weight' => 'required|numeric|min:35',
            ]);
            if ($validator->fails()) {
                $jsonResponse->setValidator($validator);
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setStatusMessage(__('generics.invalid_data'));
                return $jsonResponse->render();
            }
        } else {
            $validator = \Illuminate\Support\Facades\Validator::make($profile_data, [
                        'first_name' => 'required|string|min:4',
                        'last_name' => 'required|string|min:4',
                        'sexual_orientation' => 'required|string',
            ]);
            if ($validator->fails()) {
                $jsonResponse->setValidator($validator);
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setStatusMessage(__('generics.invalid_data'));
                return $jsonResponse->render();
            }
        }


        $user_data_raw = $request->only('display_name', 'gender', 'dob');
        $user_data = [];
        if (isset($user_data_raw['display_name'])) {
            $name = $user_data_raw['display_name'];
            do {
                $exists = User::where(['name' => $name])->where('id', '!=', $user->id)->first();
                if ($exists) {
                    $name = $user_data_raw['display_name'] . '_' . str_pad(rand(0, pow(10, 4) - 1), 4, '0', STR_PAD_LEFT);
                }
            } while ($exists);
            $user_data['name'] = $name;
        }

        if (isset($user_data_raw['dob'])) {
            $user_data['dob'] = $user_data_raw['dob'];
        }
        if (isset($user_data_raw['gender'])) {
            $user_data['gender'] = $user_data_raw['gender'];
        }

        $validator = \Illuminate\Support\Facades\Validator::make($user_data, [
                    'name' => 'required|string|min:4',
                    'gender' => 'required|string|min:4',
                    'dob' => 'required|date_format:Y-m-d|before:' . \Carbon\Carbon::now()->addYears(-18)->addDays(1),
        ]);
        if ($validator->fails()) {
            $jsonResponse->setValidator($validator);
            $jsonResponse->setHttpCode(400);
            $jsonResponse->setStatusMessage(__('generics.invalid_data'));
            return $jsonResponse->render();
        }

        $profile->fill($profile_data);
        $profile->weight = $request->get('weight', null) > 0 ? $request->get('weight', null) : null;
        $profile->height = $request->get('height', null) > 0 ? $request->get('height', null) : null;
        $profile->hourly_rate = $request->get('hourly_rate', null) > 0 ? $request->get('height', null) : null;
        $profile->languages = $request->get('languages');

        DB::beginTransaction();
        try {
            $profile->user_id = $user->id;
            $profile->save();
            $user->fill($user_data);
            $user->save();
            $file = $request->file('avatar', null);
            if ($file) {
                $user->saveImage($file);
                $user->save();
            }
            $jsonResponse->setStatusMessage(__('generics.data_saved_successfully'));
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
        $profile->user;
        $payload = new UserProfileResource($profile);
        $jsonResponse->setPayload($payload);
        return $this->renderResponse();
    }

    public function getIdentityData(Request $request, $id) {
        $user = null;
        $jsonResponse = $this->getResponseInstance();
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($id);
        } else {
            $user = $this->getUser();
            if ($user->id != $id) {
                abort(401);
            }
        }

        $profile = $user->profile;
        $profile->save();
        if (!$profile) {
            abort(404);
        }

        $ret = [];

        $frontImage = "";
        $backImage = "";
        $countryId = $profile->country_id ? $profile->country_id : $user->country_id;
        $identityId = $profile->identity_id;

        if ($user->idVerificationRequest) {
            $images = $user->idVerificationRequest->getIdImages();
            $backImage = isset($images['back']) ? $user->idVerificationRequest->getIdImageUrl($images['back'], '200x200') : '';
            $frontImage = isset($images['front']) ? $user->idVerificationRequest->getIdImageUrl($images['front'], '200x200') : '';
            $identityId = $user->idVerificationRequest->identity_id;
            $countryId = $user->idVerificationRequest->country_id;
        }

        $ret['identity_id'] = $identityId;
        $ret['country_id'] = $countryId;
        $ret['id_images'] = [
            'front' => $frontImage,
            'back' => $backImage
        ];

        $jsonResponse->setPayload($ret);
        return $this->renderResponse();
    }

    public function updateIdentity(Request $request, $id) {

        DB::beginTransaction();
        try {
            $user = null;
            $jsonResponse = $this->getResponseInstance();
            if ($this->userHasRole('ADMIN')) {
                $user = User::findOrFail($id);
            } else {
                $user = $this->getUser();
                if ($user->id != $id) {
                    abort(401);
                }
                if ($user->identity_verified) {
                    abort(401);
                }
            }



            $profile = $user->profile;
            $profile->save();
            if (!$profile) {
                abort(404);
            }

            $hasIdentityRequest = true;
            $idRequest = \App\Models\IdentityVerificationRequest::where(['user_id' => $user->id, 'status' => 'PENDING'])->first();
            if (!$idRequest) {
                $hasIdentityRequest = false;
                $idRequest = new \App\Models\IdentityVerificationRequest();
            }

            $rules = [
                'identity_id' => 'required|string|min:4',
                'country_id' => 'required|exists:countries,id',
            ];

            if (!$hasIdentityRequest) {
                $rules['id_images.front'] = 'required|image';
                $rules['id_images.back'] = 'required|image';
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                DB::rollback();
                $jsonResponse->setValidator($validator);
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setStatusMessage(__('generics.invalid_data'));
                return $jsonResponse->render();
            }

            $idRequest->identity_id = $request->get('identity_id');
            $idRequest->country_id = $request->get('country_id');
            $idRequest->user_id = $user->id;

            $idImages = $request->file('id_images');
            if ($idImages) {
                $images = $idRequest->saveIdImages($idImages);
                $attributes = $idRequest->data;
                $attributes['id_images'] = array_unique($images);
                $idRequest->data = $attributes;
            }
            $idRequest->save();

            $user->id_verification_request = $idRequest->id;
            $user->identity_verified = 0;
            $user->save();

            $ret = [];
            $ret['identity_id'] = $idRequest->identity_id;
            $ret['country_id'] = $idRequest->country_id;
            $images = $idRequest->getIdImages();
            $ret['id_images'] = [
                'front' => $idRequest->getIdImageUrl($images['front'], '200x200'),
                'back' => $idRequest->getIdImageUrl($images['back'], '200x200'),
            ];


            $jsonResponse->setPayload($idRequest);
            $jsonResponse->setStatusMessage(__('generics.data_saved_successfully'));
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
        return $this->renderResponse();
    }

    public function show(Request $request, $id) {
        $jsonResponse = $this->getResponseInstance();
        if ($this->getUser()->role != 'ADMIN' && $this->getUser()->id != $id) {
            if (!$this->getRequestLatitude() || !$this->getRequestLongitude()) {
                $jsonResponse->setHttpCode(400);
                $jsonResponse->setStatusMessage('Debe activar la ubicacion para poder ver nuestr@s chic@s!');
                return $jsonResponse->render();
            }
        }


        $user = User::findOrFail($id);

        if ($this->isUserInBlockedZone($user)) {
            abort(404);
        }


        $profile = $user->profile;
        $profile->user;
        $profile->user->country;
        $profile->user->prices;
        $profile->country;
        $profile->user->position = $profile->user->position()->distance($this->getRequestLatitude(), $this->getRequestLongitude())->first();



        if ($user->role === 'PROVIDER' && $this->getUser()->id != $id) {
            /* if (!$user->profile->is_complete) {
              abort(404);
              } */
            if ($this->getRequestCurrency() != 'USD') {
                $prices = [];
                $currency = \App\Models\CurrencyValue::where(['currency' => $this->getRequestCurrency()])->firstOrFail();
                foreach ($profile->user->prices as $key => $price) {
                    $price->currency = $this->getRequestCurrency();
                    $price->value = ($price->value * ($currency->value / 100));
                    $prices[$key] = $price;
                }
                $profile->user->prices = $prices;
                if ((!is_array($profile->user->prices)) || (!count($profile->user->prices))) {
                    abort(404);
                }
            }
            if (!$profile->user->prices) {
                abort(404);
            }
        }


        $profile->save();

        $payload = (new UserProfileResource($profile));
        $payload->actions = [
            'test' => true,
            'call' => $this->getUser()->can('make-call', $user),
            'chat' => $this->getUser()->can('init-conversation', $user),
            'reserve' => $this->getUser()->can('make-reservation', $user),
        ];

        $jsonResponse->setPayload($payload);

        return $this->renderResponse();
    }

    public function reviews(Request $request, $id, $role = null) {
        $user = User::findOrFail($id);
        $jsonResponse = $this->getResponseInstance();
        $resource = \App\Http\Resources\UserRateResource::collection($user->reviews()->with('author')->get());
        $jsonResponse->setPayload($resource);
        return $this->renderResponse();
    }

    public function prices(Request $request, $userId) {
        $user = User::findOrFail($userId);
        $jsonResponse = $this->getResponseInstance();
        $reqCurrency = $request->get('currency', 'USD');
        $prices = $user->prices;
        if ($reqCurrency != 'USD') {
            $currency = \App\Models\CurrencyValue::where(['currency' => $reqCurrency])->firstOrFail();
            foreach ($prices as $key => $price) {
                $price->currency = $this->getRequestCurrency();
                $price->value = ($price->value * ($currency->value / 100));
                $prices[$key] = $price;
            }
        }


        $jsonResponse->setPayload($prices);
        return $this->renderResponse();
    }

    public function getUserPosition(Request $request, $userId) {
        $response = $this->getResponseInstance();
        $otherUser = \App\Models\User::findOrFail($userId);
        if ($this->getUser()->can('view-position', $otherUser)) {
            $response->setPayload($otherUser->position);
            return $response->render();
        }
        abort(401);
    }

    public function updatePrices(Request $request, $userId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }

        $response = $this->getResponseInstance();

        $priceOptions = config('custom.price_options');
        $rules = [];
        foreach ($priceOptions as $opt) {
            $rules['hours_' . $opt . '.value'] = 'required|integer|min:100';
        }


        $prices = $request->get('prices', []);
        $formattedPrices = [];
        foreach ($prices as $price) {
            $price['value'] = $price['value'] * 100;

            $formattedPrices['hours_' . $price['hours']] = $price;
        }

        $validator = \Illuminate\Support\Facades\Validator::make($formattedPrices, $rules);
        if ($validator->fails()) {
            $response->setHttpCode(400);
            $response->setStatusMessage(__('generics.invalid_data'));
            $response->setValidator($validator);
            return $response->render();
        }

        $user->prices()->delete();
        foreach ($prices as $price) {
            $price['provider_id'] = $user->id;
            $price['currency'] = 'USD';
            $price['value'] = $price['value'] * 100;
            \App\Models\ProviderPrice::create($price);
        }
        $response->setPayload($user->prices);

        return $response->render();
    }

    public function updateStatus(Request $request, $userId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }
        $response = $this->getResponseInstance();
        $user->presence = ($request->get('online', false) ? 'ONLINE' : 'OFFLINE');
        $user->save();
        event(new \App\Events\UserStatusChanged($user));
        $response->setStatusMessage('Se ha cambiado tu status a ' . strtolower($user->status));
        return $response->render();
    }

    public function updateNotifications(Request $request, $userId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }
        $response = $this->getResponseInstance();
        $user->show_notifications = ($request->get('show_notifications', false));
        $user->save();
        $response->setStatusMessage('Se han guardado tus preferencias');
        return $response->render();
    }

    public function addBlockedZone(Request $request, $userId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }
        $fields = $request->only('display_name', 'polygon', 'attributes', 'enabled');
        $zone = new \App\Models\UserBlockedZone();
        $zone->fill($fields);
        $zone = $user->blockedZones()->save($zone);
        $response = $this->getResponseInstance();
        $response->setStatusMessage(__('generics.data_saved_successfully'));
        $response->setPayload($zone);
        return $response->render();
    }

    public function updateBlockedZone(Request $request, $userId, $zoneId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }

        $fields = $request->only('display_name', 'polygon', 'attributes', 'enabled');

        $zone = $user->blockedZones()->findOrFail($zoneId);
        $zone->fill($fields);
        $zone->save();
        $response = $this->getResponseInstance();
        $response->setStatusMessage(__('generics.data_saved_successfully'));
        $response->setPayload($zone);
        return $response->render();
    }

    public function removeBlockedZone(Request $request, $userId, $zoneId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }

        $user->blockedZones()->findOrFail($zoneId)->delete();
        $response = $this->getResponseInstance();
        $response->setPayload(true);
        $response->setStatusMessage(__('generics.data_saved_successfully'));
        return $response->render();
    }

    public function listBlockedZones(Request $request, $userId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }
        $zones = $user->blockedZones;
        $response = $this->getResponseInstance();
        $response->setPayload($zones);
        return $response->render();
    }

    public function getBlockedZone(Request $request, $userId, $zoneId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(401);
            }
        }

        $zone = $user->blockedZones()->findOrFail($zoneId);
        $response = $this->getResponseInstance();
        $response->setPayload($zone);
        return $response->render();
    }

}
