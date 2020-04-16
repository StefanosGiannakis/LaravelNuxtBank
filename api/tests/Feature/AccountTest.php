<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use Tests\TestCase;

class AccountTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testTranscactions()
    {
        $response = $this->json('GET', '/api/accounts/1/transactions');
        $response->assertStatus(200);
    }

    /**@test */
    public function testTransactionWithInvalidParameter()
    {
        $response = $this->json('GET', '/api/accounts/invalid/transactions');
        $response->assertStatus(422);
    }

    /**@test */
    public function testTransactionNotFoundId()
    {
        $notUsedId = \DB::table('accounts')->latest('id')
        ->first();

        ++$notUsedId->id;

        $response = $this->json('GET', "/api/accounts/$notUsedId->id/transactions");
        $response->assertStatus(404);
    }

    /**@test */
    public function testAccountWithCorrectParameter()
    {
        $response = $this->json('GET', '/api/accounts/1');
        $response->assertStatus(200);
    }

    /**@test */
    public function testAccountWithInvalidParameter()
    {
        $response = $this->json('GET', '/api/accounts/invalid');
        $response->assertStatus(422);
    }

    /**@test */
    public function testAccountNotFoundId()
    {
        $notUsedId = \DB::table('accounts')->latest('id')
        ->first();

        ++$notUsedId->id;

        $response = $this->json('GET', "/api/accounts/$notUsedId->id");
        $response->assertStatus(404);
    }

    /**@test */
    public function testMakeTransaction()
    {
        $from = 1;
        $postData = [
            'to'=>'2',
            'amount'=>'12',
            'details'=>'Test Payment on Testing DB'
        ];

        $response = $this->json('POST', "api/accounts/$from/transactions", $postData);
        $response->assertStatus(201);
    }

    /**@test */
    public function testMakeTransactionFromInvalidUser()
    {
        $notUsedId = \DB::table('accounts')->latest('id')->first();

        $from = ++$notUsedId->id;
        $postData = [
            'to'=>'2',
            'amount'=>'12',
            'details'=>'Test Payment on Testing DB Not Found User'
        ];

        $response = $this->json('POST', "api/accounts/$from/transactions", $postData);
        $response->assertStatus(422);
    }

    /**@test */
    public function testMakeTransactionInsufficientFunds()
    {
        $from = $id = 1;
        
        $getBalance = \DB::table('accounts')
                    ->select('balance')
                    ->whereRaw("id=$id")
                    ->get('balance');

        $availableBalance = $getBalance->all()[0]->balance;

        
        $postData = [
            'to'=>'2',
            'amount'=>++$availableBalance,
            'details'=>'Test Payment on Testing DB Insufficient Funds'
        ];

        $response = $this->json('POST', "api/accounts/$from/transactions", $postData);
        $response->assertStatus(405);
    }

    /**@test */
    public function testMakeTransactionSameFromAndToAccount()
    {
        
        $getAnAccountWithAvailableBalance = \DB::table('accounts')
                                        ->select(['id','balance'])
                                        ->whereRaw("balance>1")
                                        ->first();

        if(is_null($getAnAccountWithAvailableBalance)){
            \Artisan::call('migrate:fresh --seed');
            $getAnAccountWithAvailableBalance = \DB::table('accounts')
                                            ->select(['id','balance'])
                                            ->whereRaw("balance>100000000")
                                            ->first();
        }

        $availableBalance = $getAnAccountWithAvailableBalance->balance;
        $idWithBalance = $getAnAccountWithAvailableBalance->id;

        $postData = [
            'to'=>$idWithBalance,
            'amount'=>$availableBalance,
            'details'=>'Test Payment on Testing DB Not Found User'
        ];

        $response = $this->json('POST', "api/accounts/$idWithBalance/transactions", $postData);
        $response->assertStatus(422);
    }
}
