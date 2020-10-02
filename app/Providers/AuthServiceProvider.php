<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        \Illuminate\Support\Facades\Auth::provider('jwt_provider', function ($app, array $config) {
            return new JWTUserProvider($app['hash'], $config['model']);
        });

        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->role == 'ADMIN') {
                return true;
            }
        });

        Gate::define('make-reservation', function ($user, $provider) {
            return ($user->role === 'USER' || $user->role === 'GUEST') && $provider->role == 'PROVIDER' && ($user->status === 'ACTIVE' || $user->role==='GUEST') && $provider->status === 'ACTIVE';
        });

        Gate::define('view-profile', function ($user, $userTo) {
            if (config('app.mode') != 'live') {
                return false;
            }
            return true;
        });

        Gate::define('make-call', function ($userFrom, $userTo) {

            if (config('app.mode') != 'live') {
                return false;
            }

            if ($userFrom->role != 'PROVIDER') {
                return false;
            }

             $appointment = \App\Models\Appointment::where(['finished' => false, 'status_name' => 'ON_THE_WAY'])->where(function ($q) use ($userFrom, $userTo) {
                $q->where(['customer_id' => $userFrom->id, 'provider_id' => $userTo->id])
                    ->orWhere(function($q1) use ($userFrom, $userTo){
                $q1->where(['customer_id' => $userTo->id, 'provider_id' => $userFrom->id]);

                  });
            })->first();

            return $appointment;
        });

        Gate::define('init-conversation', function ($userFrom, $userTo) {
            return $userFrom->role != $userTo->role;
        });

        Gate::define('join-conversation', function ($user, $conversation) {
            $exists = $conversation->users()->where(['id' => $user->id]);
            return $exists;
        });

        Gate::define('view-position', function ($user, $otherUser) {

            if ($user->id == $otherUser->id) {
                return true;
            }
            $appointment = \App\Models\Appointment::where(['finished' => false, 'status_name' => 'ON_THE_WAY'])->where(function ($q) use ($user, $otherUser) {
                $q->where(['customer_id' => $user->id, 'provider_id' => $otherUser->id])
                    ->orWhere(['customer_id' => $otherUser->id, 'provider_id' => $user->id]);
            })->first();
            return $appointment;
        });

        Gate::define('watch-position', function ($user, $reservation) {
            return $reservation->status_name == 'ON_THE_WAY' && ($reservation->customer_id == $user->id || $reservation->provider_id == $user->id);
        });
    }
}
