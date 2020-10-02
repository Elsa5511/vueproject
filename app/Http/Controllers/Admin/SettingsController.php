<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Lib\SettingsManager;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller {

    public function index(Request $request) {
        $settings = $this->getSettings();
        return view('admin.settings.index')->with(['settings' => $settings]);
    }

    public function clearCache(Request $request) {
        SettingsManager::clearCache();
        return redirect()->back()->with('status', "Cache deleted!");
    }

    public function save(Request $request) {
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
        return redirect()->back()->with('status', "Data saved");
    }

    public function getSettings() {
        $settingsValues = [
            'extra' => [
                'title' => 'Extra',
                'values' => [
                    'user_require_phone_validation' => SettingsManager::getValue('user_require_phone_validation', false, 'boolean', true),
                    'appointments_min_interval' => SettingsManager::getValue('appointments_min_interval', 1, 'boolean', true),
                    'appointments_min_anticipation' => SettingsManager::getValue('appointments_min_anticipation', 1, 'boolean', true),
                ]
            ],
        ];

        return $settingsValues;
    }

}
