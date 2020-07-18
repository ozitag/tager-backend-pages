<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/pages', 'middleware' => ['passport:administrators', 'auth:api']], function () {
    Route::get('/pages', \OZiTAG\Tager\Backend\Blog\Controllers\AdminController::class . '@listCategories');
    Route::post('/pages', \OZiTAG\Tager\Backend\Blog\Controllers\AdminController::class . '@createCategory');
    Route::put('/pages/{id}', \OZiTAG\Tager\Backend\Blog\Controllers\AdminController::class . '@updateCategory');
    Route::get('/pages/{id}', \OZiTAG\Tager\Backend\Blog\Controllers\AdminController::class . '@viewCategory');
    Route::delete('/pages/{id}', \OZiTAG\Tager\Backend\Blog\Controllers\AdminController::class . '@removeCategory');
});
