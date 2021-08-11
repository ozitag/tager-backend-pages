<?php

use Illuminate\Support\Facades\Route;
use OZiTAG\Tager\Backend\Pages\Controllers\PublicController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminTemplatesController;
use OZiTAG\Tager\Backend\Pages\Controllers\AdminPagesController;
use OZiTAG\Tager\Backend\Rbac\Facades\AccessControlMiddleware;
use OZiTAG\Tager\Backend\Pages\Enums\PageScope;

Route::group(['middleware' => 'api.cache'], function () {
    Route::get('/tager/pages', [PublicController::class, 'index']);
    Route::get('/tager/pages/{id}', [PublicController::class, 'view']);
    Route::get('/tager/pages/view', [PublicController::class, 'viewByPath']);
});

Route::group(['prefix' => 'admin/pages', 'middleware' => ['passport:administrators', 'auth:api']], function () {

    Route::group(['middleware' => [AccessControlMiddleware::scopes(PageScope::View)]], function () {
        Route::get('/info', [AdminController::class, 'moduleInfo']);
        Route::get('/count', [AdminPagesController::class, 'count']);
        Route::get('/', [AdminPagesController::class, 'index']);


        Route::group(['middleware' => [AccessControlMiddleware::scopes(PageScope::View)]], function () {
            Route::get('/templates', [AdminTemplatesController::class, 'index']);
            Route::get('/templates/{alias}', [AdminTemplatesController::class, 'view']);
            Route::get('/{id}', [AdminPagesController::class, 'view']);
        });

        Route::group(['middleware' => [AccessControlMiddleware::scopes(PageScope::Create)]], function () {
            Route::post('/', [AdminPagesController::class, 'store']);
        });

        Route::group(['middleware' => [AccessControlMiddleware::scopes(PageScope::Edit)]], function () {
            Route::put('/{id}', [AdminPagesController::class, 'update']);
            Route::post('/{id}/move/{direction}', [AdminPagesController::class, 'move'])->where('direction', 'up|down');
        });

        Route::group(['middleware' => [AccessControlMiddleware::scopes(PageScope::Delete)]], function () {
            Route::delete('/{id}', [AdminPagesController::class, 'delete']);
        });
    });
});
