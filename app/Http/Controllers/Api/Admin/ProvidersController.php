<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\User;

/**
 * Description of ProvidersController
 *
 * @author pablo
 */
class ProvidersController extends \App\Http\Controllers\Api\BaseController
{

    public function __construct()
    {
        if (!$this->getUser() || ($this->getUser()->role != 'ADMIN')) {
            abort(403);
        }
    }

    public function all(Request $request)
    {
        $users = User::with('profile', 'country', 'prices')->where(['role' => 'PROVIDER', 'status' => 'ACTIVE'])->whereHas('prices')->orderBy('name', 'asc');
        $response = $this->getResponseInstance();
        $response->setPayload($users->get());
        return $this->renderResponse();
    }

    public function index(Request $request)
    {
        $order = $request->get('order', 'name');
        $dir = 'ASC';
        if (strpos($order, '-') === 0) {
            $dir = 'DESC';
            $order = substr($order, 1);
        }
        $users = User::with('profile', 'country')->where(['role' => 'PROVIDER'])->orderBy($order, $dir);

        if ($request->input('status') != '' && $request->input('status') != 'all') {
            if ($request->input('status') == 'pending') {
                $users->where(function ($q) {
                    $q->where(['role' => 'PROVIDER', 'identity_verified' => false]);
                });
            } elseif ($request->input('status') == 'inactive') {
                $users->where(function ($q1) {
                    $q1->where('role', '=', 'PROVIDER')->where('status', '!=', 'ACTIVE');
                });
            } else {
                $users->where(['role' => 'PROVIDER', 'identity_verified' => true, 'status' => 'active']);
            }
        }

        if ($request->input('query')) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->input('query') . '%')
                    ->orWhere('phone', 'LIKE', $request->input('query') . '%');
            });
        }

        $paginator = $users->paginate($request->get('count', 20));
        $response = $this->getResponseInstance();
        $response->setPayload($paginator);
        return $this->renderResponse();
    }

    public function show(Request $request, $id)
    {
        $response = $this->getResponseInstance();
        $response->setPayload(User::with(['profile', 'prices', 'idVerificationRequest'])->where(['role' => 'PROVIDER', 'id' => $id])->first());
        return $this->renderResponse();
    }

    public function getPrices(Request $request, $id)
    {
        $user = User::with(['profile', 'prices', 'idVerificationRequest'])->where(['role' => 'PROVIDER', 'id' => $id])->first();
        if ($request->get('currency') != 'USD') {
            $prices = [];
            $currency = \App\Models\CurrencyValue::where(['currency' => $request->get('currency')])->firstOrFail();
            foreach ($user->prices as $key => $price) {
                $price->currency = $request->get('currency');
                $price->value = ($price->value * ($currency->value / 100));
                $prices[$key] = $price;
            }
            $user->prices = $prices;
        }
        $response = $this->getResponseInstance();
        $response->setPayload($user->prices);
        return $this->renderResponse();
    }

    public function update(Request $request, $id)
    {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->firstOrFail();
        $validator = User::getValidator($request->input(), $user);
        $response = $this->getResponseInstance();
        if ($validator->fails()) {
            $response->setHttpCode(400);
            $response->setStatusMessage('Datos incorrectos');
            $response->setValidator($validator);
            return $this->renderResponse();
        }



        $user->profile()->updateOrCreate(['user_id' => $user->id], $request->input('profile', []));
        $user->update($request->input());

        $priceOptions = config('custom.price_options');
        if ($request->get('prices')) {
            $user->prices()->delete();
            $prices = collect($request->get('prices'));
            foreach ($priceOptions as $key) {
                $fprice = isset($prices[$key]) ?$prices[$key]['value'] : 0;
                $price = [];
                $price['hours'] = $key;
                $price['provider_id'] = $user->id;
                $price['currency'] = 'USD';
                $price['value'] = $fprice;
                $sPrice = \App\Models\ProviderPrice::create($price);
            }
        }

        if ($request->file('image')) {
            $user->saveImage($request->file('image'));
            $user->save();
        }

        $response->setPayload($user);


        return $this->show($request, $id);
    }

    public function getGalleryImage(Request $request, $id)
    {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        $gallery = $user->gallery;
        if ($user->gallery->isDirty()) {
            $gallery->id = \Ramsey\Uuid\Uuid::uuid4();
            $gallery->owner_id = $user->id;
            $gallery->owner_type = User::class;
            $gallery->save();
        }
        $gallery->resources;
        $response = $this->getResponseInstance();
        $response->setStatusMessage(__('generics.data_saved_successfully'));
        $response->setPayload(new \App\Http\Resources\GalleryResource($gallery));
        return $this->renderResponse();
    }

    public function saveGalleryImage(Request $request, $id)
    {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        $gallery = $user->gallery;
        if ($user->gallery->isDirty()) {
            $gallery->id = \Ramsey\Uuid\Uuid::uuid4();
            $gallery->owner_id = $user->id;
            $gallery->owner_type = User::class;
            $gallery->save();
        }

        $files = $request->file('image');

        foreach ($files as $file) {
            $resource = new \App\Models\GalleryImage();
            $resource->owner_type = \App\Models\Gallery::class;
            $resource->owner_id = $gallery->id;
            $resource->mime_type = $file->getMimeType();
            $resource->size = $file->getSize() / 1024;
            $resource->saveImage($file);
            $gallery->resources()->save($resource);
        }

        $gallery->refresh();
        $gallery->resources;

        $response = $this->getResponseInstance();
        $response->setStatusMessage(__('generics.data_saved_successfully'));
        $response->setPayload(new \App\Http\Resources\GalleryResource($gallery));
        return $this->renderResponse();
    }

    function removeGalleryImage(Request $request, $id, $galleryId)
    {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        $gallery = $user->gallery;
        if (!$gallery) {
            abort(404);
        }

        $toRemove = $request->get('delete');
        foreach ($toRemove as $fileId) {
            $resource = $gallery->resources()->where('id', $fileId)->firstOrFail();
            $resource->deleteImage();
            $resource->delete();
        }
        $gallery->resources;
        $response = $this->getResponseInstance();
        $response->setStatusMessage(__('generics.data_saved_successfully'));
        $response->setPayload(new \App\Http\Resources\GalleryResource($gallery));
        return $this->renderResponse();
    }

    function saveIdentity(Request $request, $id)
    {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile', 'country'])->firstOrFail();

        $verification = $user->idVerificationRequest;
        if (!$verification) {
            $verification = new \App\Models\IdentityVerificationRequest();
            $verification->user_id = $user->id;
            $verification->country_id = $user->country->id;
            $verification->status = 'PENDING';
        }

        $action = $request->get('action', null);
        if ($verification && $action) {
            $rules = [
                'identity_id' => 'required|string|min:4',
                'country_id' => 'required|exists:countries,id',
            ];

            if (!$verification->exists) {
                $rules['files.front'] = 'required|image';
                $rules['files.back'] = 'required|image';
            }


            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $response->setHttpCode(400);
                $response->setStatusMessage('Datos incorrectos');
                $response->setValidator($validator);
                return $this->renderResponse();
            }

            $verification->identity_id = $request->get('identity_id');
            $verification->country_id = $request->get('country_id');
            $verification->user_id = $user->id;

            $idImages = $request->file('files');
            if ($idImages) {
                $images = $verification->saveIdImages($idImages);
                $attributes = $verification->data;
                $attributes['id_images'] = is_array($attributes['id_images']) ?array_unique(array_merge($attributes['id_images'], $images)) : array_unique($images);
                $verification->data = $attributes;
            }
            $verification->save();

            $user->id_verification_request = $verification->id;
            $user->identity_verified = 0;
            $user->save();

            switch ($action) {
                case 'approve':
                    $verification->status = 'APPROVED';
                    $verification->save();
                    $user->identity_verified = 1;
                    $user->tags = '+18 Edad verificada';
                    $user->save();
                    break;
                case 'reject':
                    $verification->status = 'REJECTED';
                    $verification->save();
                    $user->tags = 'Usuario nuevo';
                    $user->identity_verified = 0;
                    $user->save();
                    break;
            }
        }
        return $this->show($request, $id);
    }


    public function updateSecurity(Request $request, $id)
    {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        if ($request->get('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->get('password'));
        }

        if ($request->get('phone_verified')) {
            $user->phone_verified = 1;
        }
        $user->save();
        if ($request->get('password')) {
            //\App\Lib\SMSManager::getInstance()->sendSMS($user->completePhone(), 'Hola ' . $user->name . ' tu nuevo password en vaceate.com es ' . $request->get('password'));
        }
        $response = $this->getResponseInstance();
        $response->setStatusMessage('Usuario guardado correctamente');
        return $this->renderResponse();
    }


    public function refereds(Request $request, $id)
    {
        $response = $this->getResponseInstance();
        $user = User::where(['role' => 'PROVIDER', 'id' => $id])->firstOrFail();
        $response->setPayload($user->refereds()->orderBy('name')->get());
        return $this->renderResponse();
    }
}
