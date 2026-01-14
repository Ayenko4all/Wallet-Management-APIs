<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Wallets Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration settings for different
    | cryptocurrency wallets used in the application. You can define
    | multiple wallets with their respective settings here.
    |
    */

    'ledger_wallet_number' => env('LEDGER_WALLET_NUMBER', 'WAL7957433'),
    'auth_token' => env('APP_AUTH_TOKEN', 'VG@123')

];
