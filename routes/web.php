<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BurialPermitController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ExhumationPermitController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\RenewalPermitController;
use App\Http\Controllers\ReportViewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationBrowserController;





Route::pattern('renewal', '[0-9]+');
Route::pattern('exhumation', '[0-9]+');
Route::pattern('cell', '[0-9]+');
Route::pattern('slot', '[0-9]+');
Route::pattern('id', '[0-9]+');
Route::pattern('reservation', '[0-9]+');


Route::get('/', fn () => view('auth.login'));
Auth::routes();

Route::group(['middleware' => 'prevent-back-history'], function () {


    Route::get('Homepage', [HomeController::class, 'homepage'])->name('Homepage');

    Route::get('logs', [HomeController::class,'logs'])->name('logs');
    
    Route::get('/apply/burial', fn () => view('burial_applicant_gate'))->name('burial.apply.gate');

    Route::get('burial_application_form', [BurialPermitController::class, 'burial_application_form'])
        ->name('burial_application_form');


    Route::get('exhumation_application_form', [HomeController::class, 'exhumation_application_form'])
        ->name('exhumation_application_form');


    Route::get('cemetery_data', [HomeController::class,'cemetery_data'])->name('cemetery_data');


    Route::get('/level/{levelNo}', [LevelController::class, 'show'])
        ->whereNumber('levelNo')->name('level.show');

    Route::get('levels/{level}/grid', [LevelController::class, 'grid'])
        ->whereNumber('level')->name('levels.grid');


    Route::get('levels/{level}/reserve', [BurialPermitController::class, 'createGrid'])
        ->whereNumber('level')->name('levels.reserve');


    // Route::get('/reports', [ReportViewController::class, 'report']);


    Route::get('list_of_users', [HomeController::class,'list_of_users'])->name('list_of_users');
    Route::get('/user_details/{id}', [HomeController::class, 'user_details'])->whereNumber('id');
    Route::post('/change_user_info', [HomeController::class, 'change_user_info']);

    Route::middleware(['auth'])->group(function () {
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });


    Route::post('/reservations', [BurialPermitController::class, 'store'])->name('reservations.store');


    Route::get('Generate_Burial_Permit',     [ReportController::class, 'Generate_Burial_Permit'])->name('Generate_Burial_Permit');
    Route::get('Generate_Exhumation_Permit', [ReportController::class, 'Generate_Exhumation_Permit'])->name('Generate_Exhumation_Permit');
    Route::get('Generate_Burial_Application_Permit', [ReportController::class, 'Generate_Burial_Application_Permit'])->name('Generate_Burial_Application_Permit');
    Route::get('Generate_report',            [ReportController::class, 'Generate_report'])->name('Generate_report');
    Route::get('print_report',               [ReportController::class, 'print_report'])->name('print_report');


    Route::get('test', [HomeController::class, 'test'])->name('test');
    Route::get('/test_list_of_users', [HomeController::class, 'test_list_of_users']);
    Route::post('/Test_edit_user',    [HomeController::class, 'Test_edit_user']);
    Route::post('/create',            [App\Http\Controllers\RegisterController::class, 'create']);
    Route::get('my_profile',          [HomeController::class,'my_profile'])->name('my_profile');

    Route::get('/api/burial-sites', [LookupController::class, 'index']);
    Route::get('/api/burial-sites/{id}/levels', [LookupController::class, 'levels'])->whereNumber('id');


    Route::get('/api/families/search', [LookupController::class, 'searchFamilies'])->name('api.families.search');
    Route::post('/api/families',       [LookupController::class, 'storeFamily'])->name('api.families.store');



Route::get('/api/families/search', [LookupController::class, 'searchFamilies'])->name('api.families.search');
Route::post('/api/families',       [LookupController::class, 'storeFamily'])->name('api.families.store');


Route::get('/api/families/{family}', [LookupController::class, 'showFamily'])
    ->whereNumber('family')
    ->name('api.families.show');

    Route::get('/api/levels/{level}/slots-progress', [LookupController::class, 'levelSlotsProgress'])
    ->whereNumber('level');


Route::put('/api/families/{family}',   [LookupController::class, 'updateFamily'])->whereNumber('family')->name('api.families.update');
Route::patch('/api/families/{family}', [LookupController::class, 'updateFamily'])->whereNumber('family');



    Route::post('exhumations', [ExhumationPermitController::class,'store'])->name('exhumations.store');
    Route::get('exhumation_form', [ExhumationPermitController::class, 'exhumation_form'])->name('exhumation.form');


    Route::get('exhumations/requests', [ExhumationPermitController::class, 'listRequests'])
        ->name('exhumations.requests');

    Route::post('exhumations/{exhumation}/approve', [ExhumationPermitController::class, 'approve'])
        ->name('exhumations.approve')->whereNumber('exhumation');

    Route::post('exhumations/{exhumation}/deny', [ExhumationPermitController::class, 'deny'])
        ->name('exhumations.deny')->whereNumber('exhumation');

    Route::post('exhumations/{exhumation}/approve-batch', [ExhumationPermitController::class, 'approveBatch'])
        ->name('exhumations.approveBatch')->whereNumber('exhumation');

        Route::get('exhumations/{exhumation}', [ExhumationPermitController::class, 'show'])
    ->name('exhumations.show')->whereNumber('exhumation');

    Route::post('/exhumations/{exhumation}/deny-batch', [ExhumationPermitController::class, 'denyBatch'])
    ->name('exhumations.denyBatch');


Route::patch('exhumations/{exhumation}', [ExhumationPermitController::class, 'update'])
    ->name('exhumations.update')->whereNumber('exhumation');



    Route::post('renewals', [RenewalPermitController::class,'store'])->name('renewals.store');


    Route::get('/renewals', [RenewalPermitController::class, 'index'])->name('renewals.index');


    Route::get('/renewals/requests', [RenewalPermitController::class, 'index'])->name('renewals.requests');


    Route::get('/renewals/datatable', [RenewalPermitController::class, 'datatable'])->name('renewals.datatable');


    Route::post('/renewals/{renewal}/approve', [RenewalPermitController::class, 'approve'])
        ->name('renewals.approve')->whereNumber('renewal');

    Route::post('/renewals/{renewal}/deny', [RenewalPermitController::class, 'deny'])
        ->name('renewals.deny')->whereNumber('renewal');

    Route::post('/renewals/{renewal}/approve-batch', [RenewalPermitController::class, 'approveBatch'])
        ->name('renewals.approveBatch')->whereNumber('renewal');

    Route::get('/renewals/{renewal}',   [RenewalPermitController::class, 'show'])
        ->name('renewals.show')->whereNumber('renewal');

    Route::patch('/renewals/{renewal}', [RenewalPermitController::class, 'update'])
        ->name('renewals.update')->whereNumber('renewal');

        Route::post('/renewals/{renewal}/deny-batch', [RenewalPermitController::class, 'denyBatch'])
    ->name('renewals.denyBatch');


    Route::get('/renewals/{renewal}/pending-by-cell', [RenewalPermitController::class, 'pendingByCell'])
    ->name('renewals.pendingByCell');
Route::patch('/renewals/{renewal}/bulk-relationships', [RenewalPermitController::class, 'bulkRelationships'])
    ->name('renewals.bulkRelationships');

    Route::get('/reservations/datatable', [ReservationController::class, 'datatable'])
        ->name('reservations.datatable');

    Route::get('/reservations/client-list', [ReservationController::class, 'list'])
        ->name('reservations.client.list');

    Route::get('/reservations/export', [ReservationController::class, 'export'])
        ->name('reservations.export');




    Route::get('/exhumations/{exhumation}/permit', [ReportController::class, 'exhumationPermit'])
        ->name('exhumations.permit')->whereNumber('exhumation');

    Route::get('/renewals/{renewal}/permit', [ReportController::class, 'generateRenewalPermit'])
        ->name('renewals.permit')->whereNumber('renewal');

Route::get('/reservations', [\App\Http\Controllers\ReservationBrowserController::class, 'index'])->name('reservations.index');
Route::get('/reservations/list', [\App\Http\Controllers\ReservationBrowserController::class, 'list'])->name('reservations.list');


Route::get('/reservations/{reservation}/permit.pdf', [ReportController::class, 'burialApplication'])
    ->name('reservations.permit.pdf')
    ->whereNumber('reservation');
    Route::get('/reservations/{reservation}/json', [ReservationBrowserController::class, 'show'])
    ->whereNumber('reservation')
    ->name('reservations.show');

   Route::put('/reservations/{reservation}', [ReservationBrowserController::class, 'update'])->name('reservations.update');




    Route::post('/cells/{cell}/slots', [\App\Http\Controllers\CellSlotController::class,'store'])
        ->whereNumber('cell')->name('cells.slots.store');

    Route::delete('/cells/{cell}/slots', [\App\Http\Controllers\CellSlotController::class, 'destroy'])
        ->whereNumber('cell')->name('cells.slots.destroy');
});
