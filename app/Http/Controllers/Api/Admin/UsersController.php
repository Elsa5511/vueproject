<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Description of ProvidersController
 *
 * @author pablo
 */
class UsersController extends \App\Http\Controllers\Api\BaseController
{

    public function __construct()
    {
        if (!$this->getUser() || ($this->getUser()->role != 'ADMIN')) {
            abort(403);
        }
    }

    public function all(Request $request)
    {
        $users = User::with('profile', 'country')->where(['role' => 'USER', 'status' => 'ACTIVE'])->orderBy('name', 'asc');
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
        $users = User::with('profile', 'country')->where(['role' => 'USER'])->orderBy($order, $dir);

        if ($request->input('status') != '' && $request->input('status') != 'all') {
            $users->where(function ($q) use ($request) {
                $q->where(['role' => 'USER', 'status' => $request->input('status')]);
            });
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
        $user = User::with(['profile', 'refereds'])->where(['role' => 'USER', 'id' => $id])->firstOrFail();
        if (!$user->profile) {
            $user->porifle = new \App\Models\UserProfile(['user_id', $user->id]);
            $user->profile->save();
        }
        $response->setPayload($user);
        return $this->renderResponse();
    }


    public function updateSecurity(Request $request, $id)
    {
        $user = User::where(['id' => $id, 'role' => 'USER'])->with(['profile'])->firstOrFail();
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

    public function update(Request $request, $id)
    {
        $user = User::where(['id' => $id, 'role' => 'USER'])->firstOrFail();
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

    public function changePassword(Request $request, $id)
    {
        $user = User::where(['role' => 'USER', 'id' => $id])->firstOrFail();
        $validator = validator($request->input(), [
            'password' => 'required|min:6'
        ]);
        $response = $this->getResponseInstance();
        if ($validator->fails()) {
            $response->setHttpCode(400);
            $response->setStatusMessage('Datos incorrectos');
            $response->setValidator($validator);
            return $this->renderResponse();
        }
        $user->password = Hash::make($request->get('password'));
        $user->save();
        return $this->show($request, $id);
    }

    public function refereds(Request $request, $id)
    {
        $response = $this->getResponseInstance();
        $user = User::where(['role' => 'USER', 'id' => $id])->firstOrFail();
        $response->setPayload($user->refereds()->orderBy('name')->get());
        return $this->renderResponse();
    }
}
