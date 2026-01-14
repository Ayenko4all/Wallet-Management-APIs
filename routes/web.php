<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'data' => ['message' => 'Welcome to the wallet management API'],
    ]);
});
