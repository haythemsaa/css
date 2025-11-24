<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'CSS Club Sportif Sfaxien API',
        'version' => '1.0.0',
        'documentation' => url('/docs'),
    ]);
});
