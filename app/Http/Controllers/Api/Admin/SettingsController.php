<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Lib\SettingsManager;
use Illuminate\Support\Facades\DB;

class SettingsController extends BaseController
{
    public function index(Request $request)
    {
        $settings = $this->getSettings();
        $response = $this->getResponseInstance();
        $response->setPayload($settings);
        return $this->renderResponse();
    }

    public function clearCache(Request $request)
    {
        SettingsManager::clearCache();
        $response = $this->getResponseInstance();
        $response->setStatusMessage('Cache borrado correctamente');
        return $this->renderResponse();
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        $inputs = $request->input('settings');
        foreach ($inputs as $key => $value) {
            $current = SettingsManager::exists($key);
            if (!$current || SettingsManager::get($key)->value != $value) {
                SettingsManager::set($key, $value);
            }
        }
        DB::commit();
        SettingsManager::clearCache();
        return $this->index($request);
    }

    public function getSettings()
    {
        $settingsValues = [
            'extra' => [
                'title' => 'Extra',
                'values' => [
                    'user_require_phone_validation' => SettingsManager::getValue('user_require_phone_validation', false, 'boolean', true),
                    'appointments_min_interval' => SettingsManager::getValue('appointments_min_interval', 1, 'boolean', true),
                    'appointments_min_anticipation' => SettingsManager::getValue('appointments_min_anticipation', 1, 'boolean', true),
                    'referer_commision' => SettingsManager::getValue('referer_commision', 0.00, 'double', true),
                ]
            ],
        ];

        return $settingsValues;
    }
}
