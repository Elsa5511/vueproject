<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib;

/**
 * Description of Utils
 *
 * @author pablo
 */
class Utils {

    public static function get(&$var, $default = null) {
        return isset($var) ? $var : $default;
    }

    public static function getByKey($key, $data, $default = null) {
        // @assert $key is a non-empty string
        // @assert $data is a loopable array
        // @otherwise return $default value
        if (!is_string($key) || empty($key) || (!$data) || (!count($data))) {
            return $default;
        }

        // @assert $key contains a dot notated string
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                // @assert $data[$innerKey] is available to continue
                // @otherwise return $default value
                if (!array_key_exists($innerKey, $data)) {
                    return $default;
                }

                $data = $data[$innerKey];
            }

            return $data;
        }

        // @fallback returning value of $key in $data or $default value
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }

    static function unique_code($limit) {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }

    static function generate_unique_code($limit) {
        $code = '';
        do {
            $code = self::unique_code($limit);
            $exists = \App\Models\User::where(['referer_code' => $code])->first();
        } while ($exists);
        return $code;
    }

}
