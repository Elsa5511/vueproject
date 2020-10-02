<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;
use App\Models\User;

class ProvidersController extends Controller {

    public function index(\App\DataTables\Admin\ProvidersDataTable $dataTable) {
        /* $savedFilter = $this->getRequestSavedParams('users.index');
          if ($savedFilter) {
          return redirect()->to(route('admin.providers.index', $savedFilter));
          }
          $this->saveRequestToSession('users.index'); */
        return $dataTable->render('admin.providers.index');
    }

    public function show(Request $request, $id) {

        return $this->edit($request, $id);
    }

    public function edit(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        if (!$user->profile) {
            $user->profile()->save(new \App\Models\UserProfile());
        }
        $user->profile;
        return view('admin.providers.detail')->with(['model' => $user, 'profile' => $user->profile, 'tab' => 'basic']);
    }

    public function editProfile(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        if (!$user->profile) {
            $user->profile()->save(new \App\Models\UserProfile());
        }
        $user->profile;
        return view('admin.providers.detail')->with(['model' => $user, 'profile' => $user->profile, 'tab' => 'profile']);
    }

    public function update(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->firstOrFail();
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
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->firstOrFail();
        $validator = User::getValidator($request->input(), $user);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $user->profile()->updateOrCreate(['user_id' => $user->id], $request->input('profile', []));
        $user->update($request->input());
        return redirect()->back()->withInput()->with(['message' => 'Usuario guardado correctamente!']);
    }

    public function editSecurity(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();

        return view('admin.providers.detail')->with(['model' => $user, 'profile' => $user->profile, 'tab' => 'security']);
    }

    public function updateSecurity(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
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
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        return $dataTable->with(['user' => $user, 'status' => $request->get('status', 'all')])->render('admin.providers.detail', ['model' => $user, 'tab' => 'appointments']);
    }

    public function showAppointment(Request $request, $id, $appointmentId) {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        $appointment = \App\Models\Appointment::where(['id' => $appointmentId, 'provider_id' => $id])->firstOrFail();
        return view('admin.appointments.detail', ['model' => $appointment, 'backUrl' => route('admin.providers.appointments', ['id' => $id])]);
    }

    public function showGallery(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        $gallery = $user->gallery;
        if ($user->gallery->isDirty()) {
            $gallery->id = \Ramsey\Uuid\Uuid::uuid4();
            $gallery->owner_id = $user->id;
            $gallery->owner_type = User::class;
            $gallery->save();
        }
        $gallery->resources;
        return view('admin.providers.detail', ['model' => $user, 'tab' => 'gallery', 'gallery' => $gallery]);
    }

    public function updateGallery(Request $request, $id) {
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
            $resource->owner_type = Gallery::class;
            $resource->owner_id = $gallery->id;
            $resource->mime_type = $file->getMimeType();
            $resource->size = $file->getSize() / 1024;
            $resource->saveImage($file);
            $gallery->resources()->save($resource);
        }

        $gallery->save();
        $gallery->resources;

        $jsonResponse = new \App\Lib\Api\JSONResponse();
        $jsonResponse->setStatusMessage(__('generics.data_saved_successfully'));
        $jsonResponse->setPayload(new \App\Http\Resources\GalleryResource($gallery));
        return $jsonResponse->render();
    }

    public function showPrices(Request $request, $id) {
        $user = User::where(['id' => $id, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();
        $priceOptions = config('custom.price_options');
        $prices = [];
        foreach ($priceOptions as $option) {
            $price = $user->prices()->where(['hours' => $option])->first();
            $value = $price ? $price->value : 0;
            $prices[$option] = $value / 100;
        }
        return view('admin.providers.detail', ['model' => $user, 'tab' => 'prices', 'prices' => $prices]);
    }

    public function updatePrices(Request $request, $userId) {
        $user = User::where(['id' => $userId, 'role' => 'PROVIDER'])->with(['profile'])->firstOrFail();

        $priceOptions = config('custom.price_options');
        $rules = [];
        foreach ($priceOptions as $opt) {
            $rules['hours_' . $opt] = 'required|integer|min:100';
        }



        $formattedPrices = [];
        foreach ($priceOptions as $opt) {
            $price = $request->get('hours_' . $opt, 0);
            $formattedPrices[('hours_' . $opt)] = $price * 100;
        }


        $validator = \Illuminate\Support\Facades\Validator::make($formattedPrices, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator)->with(['error' => __('generics.invalid_data')]);
        }

        $user->prices()->delete();
        foreach ($priceOptions as $key) {
            $fprice = $formattedPrices['hours_' . $key];
            $price = [];
            $price['hours'] = $key;
            $price['provider_id'] = $user->id;
            $price['currency'] = 'USD';
            $price['value'] = $fprice;
            $sPrice = \App\Models\ProviderPrice::create($price);
        }
        return redirect()->back()->withInput()->with(['message' => __('generics.data_saved_successfully')]);
    }

    public function deleteImageGallery(Request $request, $id) {
        $gallery = \App\Models\Gallery::findOrFail($id);
        if (!$gallery) {
            abort(404);
        }

        $toRemove = $request->get('delete');
        foreach ($toRemove as $id) {
            $resource = $gallery->resources()->where('id', $id)->firstOrFail();
            $resource->deleteImage();
            $resource->delete();
        }
        $jsonResponse = new \App\Lib\Api\JSONResponse();
        $jsonResponse->setStatusMessage(__('generics.data_saved_successfully'));
        $gallery->resources;
        $jsonResponse->setPayload(new \App\Http\Resources\GalleryResource($gallery));
        return $jsonResponse->render();
    }

    public function showIdentity(Request $request, $id) {
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
            if ($request->method() === 'PUT') {
                $rules = [
                    'identity_id' => 'required|string|min:4',
                    'country_id' => 'required|exists:countries,id',
                ];

                if (!$verification->exists) {
                    $rules['id_images.front'] = 'required|image';
                    $rules['id_images.back'] = 'required|image';
                }


                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $verification->identity_id = $request->get('identity_id');
                $verification->country_id = $request->get('country_id');
                $verification->user_id = $user->id;

                $idImages = $request->file('id_images');
                if ($idImages) {
                    $images = $verification->saveIdImages($idImages);
                    $attributes = $verification->data;
                    $attributes['id_images'] = array_unique($images);
                    $verification->data = $attributes;
                }
                $verification->save();

                $user->id_verification_request = $verification->id;
                $user->identity_verified = 0;
                $user->save();
            }

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
        } else {
            if ($action) {
                return redirect(route('admin.providers.identity', ['id' => $id]));
            }
        }


        return view('admin.providers.detail')->with(['model' => $user, 'verification' => $verification, 'tab' => 'identity']);
    }

}
