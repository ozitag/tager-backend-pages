<?php

use Illuminate\Support\Facades\Route;
use OZiTAG\Tager\Backend\Pages\Controllers\PublicController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminTemplatesController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminPagesController;

Route::group(['middleware' => 'api.cache'], function () {
    Route::get('/tager/pages', [PublicController::class, 'index']);
    Route::get('/tager/pages/{id}', [PublicController::class, 'viewById']);
    Route::get('/tager/pages/view', [PublicController::class, 'viewByPath']);
});

Route::group(['prefix' => 'admin/pages', 'middleware' => ['passport:administrators', 'auth:api']], function () {
    Route::get('/info', [AdminController::class, 'moduleInfo']);

    Route::get('/templates', [AdminTemplatesController::class, 'index']);
    Route::get('/templates/{alias}', [AdminTemplatesController::class, 'view']);

    Route::get('/count', [AdminPagesController::class, 'count']);
    Route::get('/', [AdminPagesController::class, 'index']);
    Route::post('/', [AdminPagesController::class, 'store']);
    Route::put('/{id}', [AdminPagesController::class, 'update']);
    Route::get('/{id}', [AdminPagesController::class, 'view']);
    Route::delete('/{id}', [AdminPagesController::class, 'delete']);
    Route::post('/{id}/move/{direction}', [AdminPagesController::class, 'move'])->where('direction', 'up|down');
});
