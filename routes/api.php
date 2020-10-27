<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\PaymentsContoller;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/counties', [LocationController::class, 'counties']);

Route::get('/wards', [LocationController::class, 'wards']);



Route::get('/payment_result', [PaymentsContoller::class, 'index']);

Route::post('/payment_result', [PaymentsContoller::class, 'index']);
