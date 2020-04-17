<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function getCurrencyList()
    {
        $currencies = new Currency();
        return $currencies->all();
    }
}
