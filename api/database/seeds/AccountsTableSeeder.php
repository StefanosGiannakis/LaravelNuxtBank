<?php

use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $availableCurrencies = DB::table('currencies')->pluck('id');

        DB::table('accounts')->insert([
            'name' => 'John',
            'balance' => 15000,
            'currency_id' => $availableCurrencies[mt_rand(0, count($availableCurrencies) - 1)] // Mersenne Twister rand is a better alternative to array_rand
        ]);

        DB::table('accounts')->insert([
            'name' => 'Peter',
            'balance' => 100000,
            'currency_id' => $availableCurrencies[mt_rand(0, count($availableCurrencies) - 1)]
        ]);
    }
}
