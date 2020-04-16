<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Account;

class AccountController extends Controller
{
    
    public function transactions($id)
    {
        $validator =  Validator::make(['id'=>$id],[
            'id' => 'numeric'
        ]);
        
        if($validator->fails())
            return response()->json(['error'=>'Unprocessable Entity'], 422);
        
        $id = $validator->validated()['id'];

        $transactions = \DB::table('transactions')
                        ->whereRaw("`from`=$id OR `to`=$id")
                        ->get();

        if(!count($transactions))
            return response()->json(['error'=>'Account Not Found'], 404);

        return response()->json($transactions, 200);
    }

    public function account($id)
    {
        $validator =  Validator::make(['id'=>$id],[
            'id' => 'numeric'
        ]);
        
        if($validator->fails())
           return response()->json(['error'=>'Unprocessable Entity'], 422);

        
        $id = $validator->validated()['id'];
        $account = \DB::table('accounts')
                    ->whereRaw("id=$id")
                    ->get();

        if(!count($account))
            return response()->json(['error'=>'Account Not Found'], 404);

        return response()->json($account, 200);
    }

    public function createTransaction(Request $request, $id)
    {

    }
}
