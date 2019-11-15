<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;

class CurrencyExchangeController extends Controller
{
    public function show()
    {
        return view('currency-exchange.show', ['currencies' => Currency::all()->toJson()]);
    }
}
