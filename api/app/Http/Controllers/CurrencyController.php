<?php

namespace App\Http\Controllers;

use App\Account;
use App\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function getCurrencyList()
    {
        $currencies = new Currency();
        return $currencies->all();
    }

    public function translateSenderCurrencyToReciever($validData)
    {
        $account = new Account();
        // find sender currency 
        $senderAccount = $account->find($validData['from']);

        // find reciever currency
        $recieverAccount = $account->find($validData['to']);
        
        // if is currencies code are same return 
        if($recieverAccount['currency_id'] == $senderAccount['currency_id'])
            return $validData['amount'];
 
        // convert and return the translated amount
        $currencies = $this->getCurrencyList();
        $currencies = json_decode(json_encode($currencies->values()), true);
   
        $senderCurrencyUSDrate = $this->getExchangeRate($currencies,$senderAccount['currency_id']);
        $recieverCurrencyUSDrate = $this->getExchangeRate($currencies,$recieverAccount['currency_id']);
        
        $sendersAmountToUSD = $validData['amount'] * $senderCurrencyUSDrate;

        $amountConvertedToRecieverCurrency = $sendersAmountToUSD / $recieverCurrencyUSDrate;

        return $amountConvertedToRecieverCurrency;
    }

    private function getExchangeRate($currencies,$currency_id)
    {
        foreach($currencies as $currency)
        {
            if($currency['id']==$currency_id)
                return $currency['usd_exchange_rate'];
        }
    }
}
