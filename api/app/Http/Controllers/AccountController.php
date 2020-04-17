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

        $currency_id = $account->all()[0]->currency_id;
        

        $currency = \DB::table('currencies')
                        ->select('code')
                        ->whereRaw("id=$currency_id")
                        ->get();

        $account[0]->currency = $currency[0]->code;
        
        if(!count($account))
            return response()->json(['error'=>'Account Not Found'], 404);

        return response()->json($account, 200);
    }

    public function createTransaction(Request $request, $id)
    {
        $validator =  Validator::make($request->all(),[
            'to' => 'required|numeric|exists:accounts,id',
            'amount' => 'required|numeric|min:0.1',
            'details' => 'required|min:5'
        ]);
             
        if($validator->fails())
            return response()->json($validator->errors(), 422);

        $validUserIDs = Account::where('id' ,'>' ,0)->pluck('id');
            
        // Check if `from` id is valid and is not same as `to`
        if(!in_array($id,$validUserIDs->all()) || $id==$validator->validated()['to'])
            return response()->json(['error' => 'Not a valid Id '], 422);
        
        $validData['from'] = $id;
        $validData += $validator->validated();

        $to = $validData['to'];
        $amount = $validData['amount'];
        $details = $validData['details'];

        $getBalance = \DB::table('accounts')
                        ->select('balance')
                        ->whereRaw("id=$id")
                        ->get('balance');

        $availableBalance = $getBalance->all()[0]->balance;
        if($availableBalance < $amount)
            return response()->json(['error' => 'Not available amount'], 405);


        $account = \DB::table('accounts')
                    ->whereRaw("id=$id")
                    ->update(['balance' => \DB::raw('balance-' . $amount)]);


        // Here i must translate sender currency to reciever's
        $currencyTranslator = new CurrencyController();
        $amount = $currencyTranslator->translateSenderCurrencyToReciever($validData);

        $account = \DB::table('accounts')
                    ->whereRaw("id=$to")
                    ->update(['balance' => \DB::raw('balance+' . $amount)]);

        $result = \DB::table('transactions')->insert(
            [
                'from' => $id,
                'to' => $to,
                'amount' => $amount,
                'details' => $details
            ]
        );

        if($result)
            return response()->json($validData, 201);
    }
}
