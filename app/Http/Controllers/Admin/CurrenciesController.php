<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CurrenciesController extends Controller {

    public function show(Request $request) {
        $values = Cache::rememberForever('currency_values', function() {
                    $currencies = array_filter(config('custom.currencies'), function($v, $k) {
                        return $k != 'USD';
                    }, ARRAY_FILTER_USE_BOTH);
                    $values = [];
                    foreach ($currencies as $k => $c) {
                        $value = \App\Models\CurrencyValue::firstOrNew([
                                    'currency' => $k
                                        ], ['value' => 0]);
                        $values[$k] = [
                            'label' => $c['name'],
                            'value' => $value->value
                        ];
                    }
                    return $values;
                });
        return view('admin.currencies.index', ['currencies' => $values]);
    }

    public function update(Request $request) {
        DB::beginTransaction();
        try {
            $currencies = array_filter(config('custom.currencies'), function($v, $k) {
                return $k != 'USD';
            }, ARRAY_FILTER_USE_BOTH);
            $rules = [];
            foreach ($currencies as $k => $v) {
                $rules[$k] = 'required|numeric|min:0';
            }
            $validator = validator($request->input(), $rules);
            if ($validator->fails()) {
                DB::rollback();
                return redirect()->back()->withErrors($validator)->withInput();
            }
            foreach ($currencies as $k => $c) {
                $value = \App\Models\CurrencyValue::firstOrNew([
                            'currency' => $k,
                ]);
                $value->value = $request->get($k) * 100;
                $value->save();
            }
            DB::commit();
            Cache::forget('currency_values');

            return redirect(route('admin.currencies'))->with(['message' => 'Datos guardados correctamente!']);
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => $ex->getMessage()])->withInput();
        }
    }

}
