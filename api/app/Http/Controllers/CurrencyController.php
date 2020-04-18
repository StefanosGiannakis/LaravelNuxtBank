<?php

namespace App\Http\Controllers;

use App\Account;
use App\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    private $currencies;
    
    public function __construct()
    {
        $this->currencies = $this->getCurrencyList();
        $this->currencies = json_decode(json_encode($this->currencies->values()), true);
    }

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
 
        $senderCurrencyUSDrate = $this->getCurrencyInfo($senderAccount['currency_id']);
        $recieverCurrencyUSDrate = $this->getCurrencyInfo($recieverAccount['currency_id']);
        
        $sendersAmountToUSD = $validData['amount'] * $senderCurrencyUSDrate['usd_exchange_rate'];

        $amountConvertedToRecieverCurrency = $sendersAmountToUSD / $recieverCurrencyUSDrate['usd_exchange_rate'];

        return $amountConvertedToRecieverCurrency;
    }

    public function getCurrencyInfo($currency_id)
    {
        foreach($this->currencies as $currency)
        {
            if($currency['id']==$currency_id)
                return [
                    'usd_exchange_rate' => $currency['usd_exchange_rate'],
                    'symbol' => $currency['symbol'],
                ];
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
   
        $senderCurrencyUSDrate = $this->getCurrencyInfo($senderAccount['currency_id'])['usd_exchange_rate'];
        $recieverCurrencyUSDrate = $this->getCurrencyInfo($recieverAccount['currency_id'])['usd_exchange_rate'];
        
        $recieverAmountToUSD = $transaction['amount'] * $recieverCurrencyUSDrate;

        $currency = $this->getCurrencyInfo($senderAccount['currency_id'])['symbol'];
        $transaction['amount'] = round($recieverAmountToUSD / $senderCurrencyUSDrate, 2) ." $currency";
    }
}
