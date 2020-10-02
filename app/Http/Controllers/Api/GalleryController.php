<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\GalleryImage;
use App\Models\Gallery;
use App\Models\User;

class GalleryController extends BaseController {

    public function update(Request $request, $userId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(403);
            }
            if ($user->role == 'USER') {
                if (!(\App\Lib\SettingsManager::getValue('role_user_has_gallery', false, 'bool', true))) {
                    $jsonResponse = $this->getResponseInstance();
                    $jsonResponse->setHttpCode(400);
                    $jsonResponse->setStatusMessage(__('generics.unauthorized'));
                    return $this->renderResponse();
                }
            }
        }



        $gallery = $user->gallery;
        if ($user->gallery->isDirty()) {
            $gallery->id = \Ramsey\Uuid\Uuid::uuid4();
            $gallery->owner_id = $user->id;
            $gallery->owner_type = User::class;
            $gallery->save();
        }

        $files = $request->file();
        foreach ($files as $file) {
            $resource = new GalleryImage();
            $resource->owner_type = Gallery::class;
            $resource->owner_id = $gallery->id;
            $resource->mime_type = $file->getMimeType();
            $resource->size = $file->getSize() / 1024;            
            $resource->saveImage($file);
            $gallery->resources()->save($resource);
        }

        $gallery->save();
        $gallery->resources;

        $jsonResponse = $this->getResponseInstance();
        $jsonResponse->setStatusMessage(__('generics.data_saved_successfully'));
        $jsonResponse->setPayload(new \App\Http\Resources\GalleryResource($gallery));
        return $this->renderResponse();
    }

    public function show(Request $request, $id) {
        $user = User::findOrFail($id);

        $gallery = $user->gallery;
        if ($user->gallery->isDirty()) {
            $gallery->id = \Ramsey\Uuid\Uuid::uuid4();
            $gallery->owner_id = $user->id;
            $gallery->owner_type = User::class;
            $gallery->save();
        }
        $gallery->resources;
        $jsonResponse = $this->getResponseInstance();
        $jsonResponse->setPayload(new \App\Http\Resources\GalleryResource($gallery));
        return $this->renderResponse();
    }

    public function deleteMultiple(Request $request, $userId) {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
        }

        $gallery = $user->gallery;
        if (!$gallery) {
            abort(404);
        }

        $toRemove = $request->get('remove');
        foreach ($toRemove as $id) {
            $resource = $gallery->resources()->where('id', $id)->firstOrFail();
            $resource->deleteImage();
            $resource->delete();
        }
        $jsonResponse = $this->getResponseInstance();
        $jsonResponse->setStatusMessage(__('generics.data_saved_successfully'));
        $gallery->resources;
        $jsonResponse->setPayload(new \App\Http\Resources\GalleryResource($gallery));
        return $this->renderResponse();
    }

}
