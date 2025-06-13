<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('auth.login');
});


Auth::routes();


Route::group(['middleware' => 'prevent-back-history'],function(){

    Route::get('Homepage', [HomeController::class,'homepage'])->name('Homepage');

    //Burial Application
    Route::get('burial_application_form', [HomeController::class,'burial_application_form'])->name('burial_application_form');

    // LEVELS
    Route::get('Level_1', [HomeController::class,'Level_1'])->name('Level_1');
    Route::get('Level_2', [HomeController::class,'Level_2'])->name('Level_2');

    //USERS
    Route::get('list_of_users', [HomeController::class,'list_of_users'])->name('list_of_users');
    Route::get('/user_details/{id}', [HomeController::class, 'user_details']);
    Route::post('/change_user_info', [HomeController::class, 'change_user_info'])->name('change_user_info');

    //PERMITS
    Route::get('Generate_Burial_Permit', [ReportController::class,'Generate_Burial_Permit'])->name('Generate_Burial_Permit');
    
    //Reports
    Route::get('Generate_report', [ReportController::class,'Generate_report'])->name('Generate_report');

});



