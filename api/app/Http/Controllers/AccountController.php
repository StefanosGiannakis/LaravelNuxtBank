<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    
    public function transactions($id)
    {
        $transactions = \DB::table('transactions')
        ->whereRaw("`from`=$id OR `to`=$id")
        ->get();

        return response()->json($transactions, 200);
    }
}
