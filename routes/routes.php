<?php

use Illuminate\Support\Facades\Route;
use OZiTAG\Tager\Backend\Pages\Controllers\PublicController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminController;

Route::get('/tager/pages', [PublicController::class, 'index']);
Route::get('/tager/pages/{id}', [PublicController::class, 'viewById']);
Route::get('/tager/pages/view', [PublicController::class, 'viewByPath']);

Route::group(['prefix' => 'admin/pages', 'middleware' => ['passport:administrators', 'auth:api']], function () {
    Route::get('/templates', [AdminController::class, 'templates']);
    Route::get('/templates/{alias}', [AdminController::class, 'viewTemplate']);

    Route::get('/', [AdminController::class, 'index']);
    Route::post('/', [AdminController::class, 'create']);
    Route::put('/{id}', [AdminController::class, 'update']);
    Route::get('/{id}', [AdminController::class, 'view']);
    Route::delete('/{id}', [AdminController::class, 'delete']);
});
