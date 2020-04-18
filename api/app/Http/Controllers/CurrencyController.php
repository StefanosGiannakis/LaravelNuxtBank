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

    public function translateSenderCurrencyToReciever($validData )
    {
        $account = new Account();
        // find sender  
        $senderAccount = $account->find($validData['from']);

        // find reciever 
        $recieverAccount = $account->find($validData['to']);
        
        // if currencies code are same return 
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

    public function translateCurrencyToSender(&$transaction)
    {
        $account = new Account();
        // find sender  
        $senderAccount = $account->find($transaction['from']);

        // find reciever 
        $recieverAccount = $account->find($transaction['to']);
        
        // if currencies code are same return 
        if($recieverAccount['currency_id'] == $senderAccount['currency_id'])
            return $transaction['amount'];
 
        // convert and return the translated amount
        $currencies = $this->getCurrencyList();
        $currencies = json_decode(json_encode($currencies->values()), true);
   
        $senderCurrencyUSDrate = $this->getExchangeRate($currencies,$senderAccount['currency_id']);
        $recieverCurrencyUSDrate = $this->getExchangeRate($currencies,$recieverAccount['currency_id']);
        
        $recieverAmountToUSD = $transaction['amount'] * $recieverCurrencyUSDrate;

        $transaction['amount'] = round($recieverAmountToUSD / $senderCurrencyUSDrate, 2);
    }
}
