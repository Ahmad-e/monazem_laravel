<?php

namespace App\Http\Controllers;

use App\Models\Currencies;
use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    public function showCurrencies(){
        $data = Currencies::get();

        return response()->json([
            'state' => 200,
            'data'=>$data,
        ], 200);
    }

    public function addCurrencies(Request $request){

        $request->validate([
            'code_en' => 'required',
            'code_ar' => 'required'
        ]);

        Currencies::create([
            'code_en' => $request->code_en,
            'code_ar' => $request->code_ar,
            'symbol' => $request->symbol,
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'exchange_rate_to_dollar' => $request->exchange_rate_to_dollar
        ]);

        $data = Currencies::get();

        return response()->json([
            'state' => 200,
            'data'=>$data,
        ], 200);
    }


    public function updateCurrency(Request $request, $id)
    {
        $currency = Currencies::find($id);

        if (!$currency) {
            return response()->json([
                'state' => 404,
                'message' => "Currency not found",
            ], 404);
        }


        $currency->update($request->only([
            'code_en',
            'code_ar',
            'symbol',
            'name_en',
            'name_ar',
            'exchange_rate_to_dollar'
        ]));


        $data = Currencies::get();

        return response()->json([
            'state' => 200,
            'message' => "Currency updated successfully",
            'data' => $data,
        ]);
    }


    public function toggleBlockedCurrency($id)
    {
        $currency = Currencies::find($id);

        if (!$currency) {
            return response()->json([
                'state' => 404,
                'message' => "Currency not found",
            ], 404);
        }

        // عكس القيمة
        $currency->blocked_currency = !$currency->blocked_currency;
        $currency->save();
        $data = Currencies::get();
        return response()->json([
            'state' => 200,
            'message' => "Currency blocked status updated",
            'data' => $data,
        ]);
    }

    public function addMultipleCurrencies(Request $request)
    {
        // تحقق من أن الطلب يحتوي على مصفوفة من العملات
        $request->validate([
            'currencies' => 'required|array',
            'currencies.*.currencies_code' => 'required|string',
            'currencies.*.currencies_code_ar' => 'required|string',
            'currencies.*.currencies_symbol' => 'required|string',
            'currencies.*.currencies_name_en' => 'required|string',
            'currencies.*.currencies_name_ar' => 'required|string',
            'currencies.*.currencies_exchange_rate_to_dollar' => 'required|numeric',
        ]);

        // إدخال البيانات في قاعدة البيانات
        foreach ($request->currencies as $currencyData) {
            Currencies::create([
                'code_en' => $currencyData['currencies_code'],
                'code_ar' => $currencyData['currencies_code_ar'],
                'symbol' => $currencyData['currencies_symbol'],
                'name_en' => $currencyData['currencies_name_en'],
                'name_ar' => $currencyData['currencies_name_ar'],
                'exchange_rate_to_dollar' => $currencyData['currencies_exchange_rate_to_dollar'],
            ]);
        }
        $data = Currencies::get();
        return response()->json([
            'state' => 200,
            'data'=>$data,
            'message' => "Currencies added successfully",
        ], 201);
    }
}
