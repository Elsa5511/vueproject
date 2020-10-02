<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Description of SettingsManager
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class SettingsManager
{

    public static $all = array();
    public static $byKey = array();
    public static $loaded = false;

    public static function loadSettings($force = false)
    {

        if (!$force && self::$all) {
            return self::$all;
        }
        if ($force) {
            self::clearCache();
        }
        if (config('app.env') == 'local') {
            self::$all = self::loadFromDb();
        } else {
            self::$all = Cache::rememberForever('system_settings', function () {
                return self::loadFromDb();
            });
        }
        foreach (self::$all as $k) {
            self::$byKey[$k->name] = $k;
        }
        self::$loaded = true;
        return self::$all;
    }

    private static function loadFromDb()
    {
        $keysRaw = DB::select(DB::raw("SELECT DISTINCT(name) FROM settings"));
        $keys = array();
        self::$all = array();
        self::$byKey = array();
        foreach ($keysRaw as $k) {
            $setting = DB::select(DB::raw("SELECT * FROM settings WHERE name='{$k->name}' ORDER BY id DESC LIMIT 1"));
            $setting[0]->value = json_decode($setting[0]->value);
            self::$all[] = $setting[0];
        }
        return self::$all;
    }

    public static function clearCache()
    {
        Cache::forget('system_settings');
    }

    public static function set($key, $value, $type = 'string', $description = '')
    {

        $value = self::cast($value, $type);
        if ($existant = Setting::where(['name' => $key])->first()) {
            $existant->fill([
                'name' => $key,
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]);
            $existant->save();
        } else {
            Setting::create([
                'name' => $key,
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]);
        }

        self::clearCache();
        self::loadSettings(true);
    }

    public static function all()
    {
        return self::loadSettings();
    }

    private static function cast($value, $type)
    {
        if (!$value) {
            return $value;
        }
        switch ($type) {
            case 'int':
                return (int)$value;
            case 'double':
                return (double)$value;
            case 'bool':
            case 'boolean':
                return (bool)$value;
            default:
                return $value;
        }
    }

    public static function getValue($key, $default = null, $type = 'string', $autoSave = false)
    {
        try {
            $setting = self::get($key);
            if (!$setting) {
                if ($autoSave) {
                    self::set($key, $default, $type);
                }
                return self::cast($default, $type);
            }
            return self::cast($setting->value, $type);
        } catch (\Exception $ex) {
            return $default;
        }
    }

    public static function get($key)
    {
        if (!self::$loaded) {
            self::loadSettings(true);
        }
        if (isset(self::$byKey[$key])) {
            return self::$byKey[$key];
        }
        return null;
    }

    public static function exists($key)
    {
        return isset(self::$byKey[$key]);
    }

    public static function forget($key, $onlyLast = false)
    {
        return Setting::delete(['name' => $key]);
    }
}
