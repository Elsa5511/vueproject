<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Cache;

/**
 * Description of PositionsController
 *
 * @author pablo
 */
class PositionsController extends BaseController {

    public function updatePosition(\Illuminate\Http\Request $request) {
        $response = $this->getResponseInstance();
        try {

            $user = $this->getUser();
            $type = $request->get('type', 'default');
            if ($user->role === 'PROVIDER') {
                $type = 'eager';
            }

            $currentPosition = cache()->rememberForever('positions_' . $user->id, function () use ($user) {
                return $user->position;
            });

            if (false && $currentPosition) {
                if ($currentPosition->created_at->diffInSeconds(\Illuminate\Support\Carbon::now()) < config('custom.position_traker.' . $type . '_interval')) {
                    $response->setPayload($currentPosition);
                    $response->setStatusMessage('Skipped position, last saved: ' . $currentPosition->created_at);
                    event(new \App\Events\PositionChangedEvent($currentPosition));
                    return $response->render();
                }
            }

            $position = \App\Models\Position::create([
                        'latitude' => $request->get('latitude'),
                        'longitude' => $request->get('longitude'),
                        'altitude' => $request->get('altitude'),
                        'accuracy' => $request->get('accuracy'),
                        'heading' => $request->get('heading'),
                        'speed' => $request->get('speed'),
                        'user_id' => $user->id,
            ]);

            event(new \App\Events\AdminEvent('POSITION_CHANGED', ['position' => $position, 'user' => $user]));
            cache()->forever('positions_' . $user->id, $position);
            $user->position_id = $position->id;
            $user->save();
            $response->setPayload($position);
        } catch (\Exception $ex) {
            $response->setHttpCode(500);
            $response->setStatusMessage($ex->getMessage());
        }
        return $response->render();
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

    public function calculateDistance(Request $request, $userId) {
        $response = $this->getResponseInstance();
        $query = \App\Models\User::where(['id' => $userId])->where(function($q) use ($request) {
            $q->whereHas('position', function($q) use ($request) {
                $q->distance($this->getRequestLatitude(), $this->getRequestLongitude());
            });
        });
        $response->setPayload($query->first()->distance);
        return $response->render();
    }

    public function getPositions() {
        $user = $this->getUser();
        if (!$user || $user->role != 'ADMIN') {
            abort(401);
        }
        $positions = [];
        $providers = \App\Models\User::where(['role' => 'PROVIDER'])->with('position')->get();
        foreach ($providers as $p) {
            if ($p->position) {
                $positions[] = [
                    'user' => $p,
                    'position' => $p->position,
                ];
            }
        }
        $response = $this->getResponseInstance();
        $response->setPayload($positions);
        return $response->render();
    }

}
