<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\MappingExtentionController;
use App\Http\Controllers\Api\UploaderController;
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
