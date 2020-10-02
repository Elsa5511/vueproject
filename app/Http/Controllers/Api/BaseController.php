<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Lib\Api\JSONResponse;

class BaseController extends Controller
{

    /**
     *
     * @var \App\Models\User
     */
    private $user = null;

    /**
     *
     * @var \App\Lib\Api\JSONResponse;
     */
    private $responseInstance = null;

    /**
     * 
     * @return User
     */
    protected function getUser()
    {
        if ($this->user == null) {
            $this->user = auth('api')->user();
        }
        if ($this->user) {
            $this->user->referer_code;
        }
        return $this->user;
    }

    protected function getRequestLatitude()
    {
        return is_numeric(request()->header('x-lat')) ? (float)request()->header('x-lat') : null;
    }

    protected function getRequestLongitude()
    {
        return is_numeric(request()->header('x-lng')) ? (float)request()->header('x-lng') : null;
    }

    protected function getRequestCurrency()
    {
        return request()->header('x-currency', 'USD');
    }

    protected function isUserInBlockedZone($otherUser)
    {
        if ($this->getUser()->id != $otherUser->id && $this->getUser()->role != 'ADMIN') {
            $zones = $otherUser->blockedZones()->get();
            foreach ($zones as $zone) {
                $json = json_decode($zone->polygon);
                $distance = \App\Lib\GeoLocation::distanceOfLatLng($this->getRequestLatitude(), $this->getRequestLongitude(), $json->geometry->coordinates[1], $json->geometry->coordinates[0], 'MT');
                if ($distance < $json->properties->radius) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function getToken()
    {
        return auth('api')->getToken();
    }

    protected function getAuthPayload()
    {
        return auth('api')->payload();
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function userHasRole($roles = array())
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }
        if (is_array($roles) && in_array($user->role, $roles)) {
            return true;
        } elseif ($user->role == $roles) {
            return true;
        }
        return false;
    }

    public function requireRole($roles = array())
    {
        if (!$this->userHasRole($roles)) {
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException();
        }
    }

    public function addNotifications()
    {
        if (!$this->getUser()) {
            return;
        }
        if (($this->getUser()->status === 'PENDING' || (!$this->user->identity_verified)) && $this->getUser()->role === 'PROVIDER' && config('app.mode') === 'live') {
            $this->responseInstance->addNotification('Estamos validando tu usuario, no aparecerás en los listados hasta que completemos la validación!');
        } else if (!$this->getUser()->has_complete_profile) {
            //$this->responseInstance->addNotification('Tu perfil no está completo, por favor ve a la sección perfil y coplétalo!');
        }
    }

    /**
     * 
     * @return JSONResponse
     */
    public function getResponseInstance()
    {
        if ($this->responseInstance == null) {
            $this->responseInstance = new JSONResponse();
        }
        $this->addNotifications();

        return $this->responseInstance;
    }

    public function renderResponse()
    {
        return $this->getResponseInstance()->render();
    }
}
