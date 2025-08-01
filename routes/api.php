<?php

use App\Http\Controllers\Api\BurialPermitApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginApiController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\RegisterApiController;
use App\Http\Controllers\Api\RenewalPermitApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/testing', function(){
   return 'API SUCCESSFULLY GENERATED';
});

Route::post('/login', [LoginApiController::class, 'login']);
Route::post('/register', [RegisterApiController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
Route::get('/reservations-list', [RenewalPermitApiController::class, 'index']);
Route::get('/renewals-list', [RenewalPermitApiController::class, 'listOfRenewals']);







});
