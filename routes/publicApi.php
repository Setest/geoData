<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * geoData test data route
 */

Route::get('test-geodata', function (Request $request) {
    return response()->json([
        'country' => Request::get('geoDataCountry'),
        'data'    => Request::get('geoData')
    ]);
})->middleware('geoData');
