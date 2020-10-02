<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CurrenciesController extends \App\Http\Controllers\Api\BaseController
{

    public function show(Request $request)
    {
        $values = Cache::rememberForever('currency_values', function () {
            $currencies = array_filter(config('custom.currencies'), function ($v, $k) {
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

        $response = $this->getResponseInstance();
        $response->setPayload($values);
        return $this->renderResponse();
    }

    public function update(Request $request)
    {
        $response = $this->getResponseInstance();
        DB::beginTransaction();
        try {
            $currencies = array_filter(config('custom.currencies'), function ($v, $k) {
                return $k != 'USD';
            }, ARRAY_FILTER_USE_BOTH);
            $rules = [];
            foreach ($currencies as $k => $v) {
                $rules[$k] = 'required|numeric|min:0';
            }
            $validator = validator($request->input(), $rules);
            if ($validator->fails()) {
                DB::rollback();
                $response->setValidator($validator);
                return $this->renderResponse();
            }
            foreach ($currencies as $k => $c) {
                $value = \App\Models\CurrencyValue::firstOrNew([
                    'currency' => $k,
                ]);
                $value->value = $request->get($k);
                $value->save();
            }
            DB::commit();
            Cache::forget('currency_values');


            return $this->show($request);
        } catch (\Exception $ex) {
            DB::rollback();
            $response->setStatusMessage($ex->getMessage());
            $response->setHttpCode(500);
        }

        return $this->renderResponse();
    }
}
