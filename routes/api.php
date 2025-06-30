<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\MappingExtentionController;
use App\Http\Controllers\Api\UploaderController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\IndexController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('webhook/update/customer', [WebhookController::class, 'ghlContactToNoloco'])->name('ghl.to.noloco')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('webhook/deal/to/crm', [WebhookController::class, 'nolocoToGhl'])->name('noloco.to.ghl')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::post('/list', [InventoryController::class, 'index']);
    Route::get('/list/data', [InventoryController::class, 'index']);
    Route::get('/get/{id}', [InventoryController::class, 'getSpecificInv'])->name('fetch');
    Route::get('/settings', [InventoryController::class, 'getSettings']);
});
Route::prefix('upload-image')->name('upload-image.')->group(function () {
    Route::post('/', [UploaderController::class, 'uploadFile']);
    Route::get('/list', [UploaderController::class, 'getFiles']);
});


Route::prefix('mapping-extention')->name('mapping-extention.')->group(function () {
    Route::post('/store', [MappingExtentionController::class, 'store']);
    Route::get('/fetch/specific', [MappingExtentionController::class, 'getMapUrl']);
    Route::post('/search', [MappingExtentionController::class, 'search']);
});


// 700 app


Route::prefix('credit-report')->name('credit-report.')->group(function () {
    Route::POST('/list', [CreditController::class, 'list']);
    Route::POST('/settle', [CreditController::class, 'setReport']);
    Route::POST('/check/valid/location', [CreditController::class, 'checkValidLocation']);
});


Route::post('/manage/conatct/fields', [IndexController::class, 'manageContactFields'])->name('manage.conatct.fields')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
