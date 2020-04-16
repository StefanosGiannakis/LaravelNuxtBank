<?php

use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insert([
            'code'  => 'USD',
            'name'  => 'US Dollar',
            'symbol'=>  '$',
            'usd_exchange_rate' => 1,
            'currency_verified_at'  =>  now()
        ]);
        
        DB::table('currencies')->insert([
            'code'  => 'EUR',
            'name'  => 'EURO',
            'symbol'=>  '€',
            'usd_exchange_rate' => 1.09,
            'currency_verified_at'  =>  now()
        ]);

        DB::table('currencies')->insert([
            'code'  => 'GBP',
            'name'  => 'Pound Sterling',
            'symbol'=>  '£',
            'usd_exchange_rate' => 1.25,
            'currency_verified_at'  =>  now()
        ]);
    }
}
