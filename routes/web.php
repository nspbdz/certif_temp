<?php

use App\Http\Controllers\SampleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserActivityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group([
    'prefix' => '',
    'middleware' => ['checklogin'],
    'as' => 'backend.'
], function () {
    Route::get('', [AuthController::class, 'auth']);
    Route::get('auth', [AuthController::class, 'auth'])->name('login');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('callback', [AuthController::class, 'callback'])->name('callback')->withoutMiddleware('checklogin');
    Route::post('template/image', [TemplateController::class, 'image'])->name('template.image');
    Route::get('template/datatable', [TemplateController::class, 'datatable'])->name('template.datatable');
    Route::resource('template', TemplateController::class);
    Route::get('table', [SampleController::class, 'table'])->name('table');
    // Route::get('table/detail/{id?}', [SampleController::class, 'detail'])->name('table.detail');
    Route::get('datatable', [SampleController::class, 'datatable'])->name('datatable');
    Route::post('campaign/data', [CampaignController::class, 'data'])->name('campaign.data');
    Route::get('campaign/datatable', [CampaignController::class, 'datatable'])->name('campaign.datatable');
    Route::resource('campaign', CampaignController::class);
    Route::resource('sample', SampleController::class);
    Route::get('recipient/datatable', [RecipientController::class, 'datatable'])->name('recipient.datatable');
    Route::post('recipient/import/{id?}', [RecipientController::class, 'import'])->name('recipient.import');
    Route::get('recipient/download', [RecipientController::class, 'download'])->name('recipient.download');
    Route::get('recipient/send', [RecipientController::class, 'send'])->name('recipient.send');
    Route::get('recipient/retry', [RecipientController::class, 'retry'])->name('recipient.retry');
    Route::get('recipient/delete', [RecipientController::class, 'delete'])->name('recipient.delete');
    Route::get('recipient/get_status_total/{id}', [RecipientController::class, 'get_status_total'])->name('recipient.get_status_total');
    Route::get('recipient/{id?}', [RecipientController::class, 'index'])->name('recipient.index');
    Route::resource('user_activity', UserActivityController::class)->except(['show']);
    Route::get('user_activity/datatable', [UserActivityController::class, 'datatable'])->name('user_activity.datatable');
    Route::get('user_activity/export_excel/{username?}/{start_date?}/{end_date?}', [UserActivityController::class, 'export_excel'])->name('user_activity.export_excel');

   
});
