<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/pages', 'middleware' => ['passport:administrators', 'auth:api']], function () {
    Route::get('/pages', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@index');
    Route::post('/pages', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@create');
    Route::put('/pages/{id}', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@update');
    Route::get('/pages/{id}', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@view');
    Route::delete('/pages/{id}', \OZiTAG\Tager\Backend\Pages\Controllers\AdminController::class . '@delete');
});
