<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BurialPermitController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ExhumationPermitController;
use App\Http\Controllers\ExhumationApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RenewalPermitController;
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
    Route::get('burial_application_form', [BurialPermitController::class, 'burial_application_form'])->name('burial_application_form');
    Route::get('/row_list/{id}', [BurialPermitController::class, 'row_list']);
    Route::get('/col_list/{id}', [BurialPermitController::class, 'col_list']);

    //EXHUMATION
    Route::get('exhumation_application_form', [HomeController::class, 'exhumation_application_form'])->name('exhumation_application_form');

    //CEMETERY ALL DATA
    Route::get('cemetery_data', [HomeController::class,'cemetery_data'])->name('cemetery_data');

    // LEVELS
    Route::get('/level/{levelNo}', [LevelController::class, 'show'])
     ->whereNumber('levelNo')
     ->name('level.show'); // dynamic levels


    //USERS
    Route::get('list_of_users', [HomeController::class,'list_of_users'])->name('list_of_users');
    Route::get('/user_details/{id}', [HomeController::class, 'user_details']);
    Route::post('/change_user_info', [HomeController::class, 'change_user_info']);

    //PERMITS
    Route::get('Generate_Burial_Permit', [ReportController::class,'Generate_Burial_Permit'])->name('Generate_Burial_Permit');
    Route::get('Generate_Exhumation_Permit', [ReportController::class,'Generate_Exhumation_Permit'])->name('Generate_Exhumation_Permit');

    //Reports
    Route::get('Generate_report', [ReportController::class,'Generate_report'])->name('Generate_report');
    Route::get('print_report', [ReportController::class,'print_report'])->name('print_report');

    //test
    Route::get('test', [HomeController::class,'test'])->name('test');
    Route::get('/test_list_of_users', [App\Http\Controllers\HomeController::class, 'test_list_of_users']);
    Route::post('/Test_edit_user', [App\Http\Controllers\HomeController::class, 'Test_edit_user']);
    Route::post('/create', [App\Http\Controllers\RegisterController::class, 'create']);

    //Burial Application Main Controllers
    Route::get('/burialAppView',[BurialPermitController::class, 'burialAppView']);




    //for testing only to be finalized


    Route::get ('/levels/{level}/reserve',
    [App\Http\Controllers\BurialPermitController::class,'createGrid']
)->whereNumber('level')->name('reservations.createGrid');

Route::post('/reservations',
    [App\Http\Controllers\BurialPermitController::class,'store']
)->name('reservations.store');

Route::get('/level/{levelNo}', [LevelController::class, 'show'])
     ->whereNumber('levelNo')
     ->name('level.show');

     Route::get('levels/{level}/grid',
    [LevelController::class, 'grid']
)->name('levels.grid');

      Route::get(
        '/api/burial-sites/{site}/levels',
        [BurialPermitController::class, 'levels']
    )->name('api.levels.for-site');


    Route::post('exhumations', [ExhumationPermitController::class,'store'])
     ->name('exhumations.store');

     Route::get('exhumation_form', [ExhumationPermitController::class, 'exhumation_form']);



Route::get('exhumations/requests',
    [App\Http\Controllers\ExhumationPermitController::class, 'listRequests']
)->name('exhumations.requests');

Route::post('exhumations/{exhumation}/approve',
    [ExhumationPermitController::class, 'approve']
)->name('exhumations.approve');

Route::post('exhumations/{exhumation}/deny',
    [ExhumationPermitController::class, 'deny']
)->name('exhumations.deny');


/// renewals

// Route::get('renewal-requests',
//   [RenewalPermitController::class, 'index'])->name('renewals.index');

// Route::resource('renewals', RenewalPermitController::class)
//      ->only(['index','update']);
 Route::post('renewals',                    [RenewalPermitController::class,'store'])->name('renewals.store');
// Route::post('renewals/{renewal}/approve',  [RenewalPermitController::class,'approve'])->name('renewals.approve');
// Route::post('renewals/{renewal}/deny',     [RenewalPermitController::class,'deny'])->name('renewals.deny');

Route::prefix('renewals')->name('renewals.')->group(function () {
    Route::get('/',                [RenewalPermitController::class, 'index'])->name('index');
    Route::get('/datatable',       [RenewalPermitController::class, 'datatable'])->name('datatable');

    Route::post('/{renewal}/approve', [RenewalPermitController::class, 'approve'])->name('approve');
    Route::post('/{renewal}/deny',    [RenewalPermitController::class, 'deny'])->name('deny');




});
});



