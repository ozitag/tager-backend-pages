<?php

use Illuminate\Support\Facades\Route;

Route::get('/tager/pages', \OZiTAG\Tager\Backend\Pages\Controllers\PublicController::class . '@index');
Route::get('/tager/pages/{id}', \OZiTAG\Tager\Backend\Pages\Controllers\PublicController::class . '@viewById');
Route::get('/tager/pages/view', \OZiTAG\Tager\Backend\Pages\Controllers\PublicController::class . '@viewByPath');

Route::group(['prefix' => 'admin/pages', 'middleware' => ['passport:administrators', 'auth:api']], function () {
    Route::get('/templates', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@templates');
    Route::get('/templates/{alias}', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@viewTemplate');

    Route::get('/', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@index');
    Route::post('/', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@create');
    Route::put('/{id}', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@update');
    Route::get('/{id}', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@view');
    Route::delete('/{id}', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@delete');
});
