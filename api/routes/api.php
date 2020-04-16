<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
*/

Route::get('accounts/{id}', 'AccountController@account')->name('getAccount');

Route::get('accounts/{id}/transactions', 'AccountController@transactions')->name('getTransactions');

Route::post('accounts/{id}/transactions', 'AccountController@createTransaction')->name('createTransaction');

Route::get('currencies', function () {
    $account = DB::table('currencies')
              ->get();

    return $account;
});
