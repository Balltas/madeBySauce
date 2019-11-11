<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Currency;
use DB;

class CurrencyController extends Controller
{
    public function main(Request $request)
    {
        $request->validate([
            'from' => 'required|max:3|alpha_num',
            'to' => 'required|max:3|alpha_num',
            'amount' => 'required|numeric',
        ]);

        $currencies = Currency::where('code', $request->from)
            ->orWhere('code', $request->to)
            ->get();

        if (empty($currencies[0]) ||  empty($currencies[1])) {
            return response()->json([
                'response' => 'failure',
                'message' => 'We apologise. We currently can\'t process your request by given data. It is possible that 
                    currency conversion rates are missing. Hopefully we will provide it in near future.',
                'data' => '',
            ]);
        }

        $currencyRate = DB::table('currency_rates')
            ->select('currency_rates.rate')
            ->join('currencies as c1', 'currency_rates.currency_from_id', '=', 'c1.id')
            ->join('currencies as c2', 'currency_rates.currency_to_id', '=', 'c2.id')
            ->where('c1.code', $request->from)
            ->where('c2.code', $request->to)
            ->first();

        if (empty($currencyRate)) {
            $currencyRate = DB::table('currency_rates')
                ->select('currency_rates.rate')
                ->join('currencies as c1', 'currency_rates.currency_to_id', '=', 'c1.id')
                ->join('currencies as c2', 'currency_rates.currency_from_id', '=', 'c2.id')
                ->where('c1.code', $request->from)
                ->where('c2.code', $request->to)
                ->first();

            if (!empty($currencyRate->rate)) {
                $currencyRate->rate = 1/$currencyRate->rate;
            }
        }

        if (!empty($currencyRate->rate)) {
            return response()->json([
                'response' => 'success',
                'message' => '',
                'data' => [
                    'from' => $request->from,
                    'to' => $request->to,
                    'amount' => $request->amount,
                    'converted_amount' => $request->amount * $currencyRate->rate,
                ],
            ]);
        }

        return response()->json([
            'response' => 'failure',
            'message' => 'We apologise. We currently can\'t process your request by given data. It is possible that 
                    currency conversion rates are missing. Hopefully we will provide it in near future.',
            'data' => '',
        ]);

    }
}
