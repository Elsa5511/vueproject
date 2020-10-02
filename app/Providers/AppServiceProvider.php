<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use \Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Carbon::serializeUsing(function ($carbon) {
            return $carbon->format('Y/m/d H:i:s');
        });
        Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        Validator::extend('text_required', function ($attribute, $value, $parameters) {
            $value = preg_replace("/\s|&nbsp;/", '', $value);
            return strip_tags($value);
        });

        Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            return preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $value) && strlen($value) >= 10 && (substr($value, 0, 1) != '1');
        });

        Validator::replacer('phone', function($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, ':attribute is invalid phone number');
        });

        Validator::extend('email_quality', function($attribute, $value, $parameters, $validator) {
            $quality = \App\Lib\FraudCheckApi::getInstance()->validEmail($value);
            if ($quality == 'BAD_EMAIL' || !$quality) {
                return false;
            }
            return true;
        });

        \Illuminate\Support\Facades\Validator::extend('userUniquePhone', function ($attribute, $value, $parameters, $validator) {
            $raw = DB::select(DB::raw('SELECT users.id, users.phone, countries.phonecode FROM users LEFT JOIN countries ON(countries.id=users.country_id) WHERE users.phone=:phone and countries.phonecode=:code LIMIT 1'), ['phone' => $value, 'code' => $parameters[1]]);
            return !count($raw);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
