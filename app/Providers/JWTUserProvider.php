<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider as UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use App\Models\User;

/**
 * Description of JWTUserProvider
 *
 * @author pablo
 */
class JWTUserProvider extends UserProvider {

    protected function getGuestUser($id = null) {
        $user = new User([
            'id' => $id ? $id : \Ramsey\Uuid\Uuid::uuid4(),
            'name' => 'Guest',
        ]);
        $user->role = 'GUEST';
        return $user;
    }

    /**
     * @param    mixed  $identifier
     * @return  \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier) {
        $token = \App\Models\UserToken::where('user_id', $identifier)->first();
        if (!$token) {
            return null;
        }
        if ($token->is_user) {
            $user = $token->user;
        } else {
            $user = $this->getGuestUser($identifier);
        }
        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param    \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param    array  $credentials
     * @return  bool
     */
    public function validateCredentials(UserContract $user, array $credentials) {
        $plain = $credentials['password'];
        return $this->hasher->check($plain, $user->getAuthPassword());
    }

}
