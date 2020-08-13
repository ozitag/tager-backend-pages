<?php

use Illuminate\Support\Facades\Route;
use OZiTAG\Tager\Backend\Pages\Controllers\PublicController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminTemplatesController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminPagesController;

Route::get('/tager/pages', [PublicController::class, 'index']);
Route::get('/tager/pages/{id}', [PublicController::class, 'viewById']);
Route::get('/tager/pages/view', [PublicController::class, 'viewByPath']);

Route::group(['prefix' => 'admin/pages', 'middleware' => ['passport:administrators', 'auth:api']], function () {
    Route::get('/templates', [AdminTemplatesController::class, 'index']);
    Route::get('/templates/{alias}', [AdminTemplatesController::class, 'view']);

    Route::get('/', [AdminPagesController::class, 'index']);
    Route::post('/', [AdminPagesController::class, 'store']);
    Route::put('/{id}', [AdminPagesController::class, 'update']);
    Route::get('/{id}', [AdminPagesController::class, 'view']);
    Route::delete('/{id}', [AdminPagesController::class, 'delete']);
});
