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
    public function testExample()
    {
        $response = $this->json('GET', '/api/accounts/1/transactions');
        $response->assertStatus(200);
    }
}
