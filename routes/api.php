<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrokersController;
use App\Http\Controllers\EnquiriesController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/enquiries', [EnquiriesController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/register', [AuthController::class, 'register']);
        Route::apiResource('/users', UsersController::class);
        Route::get('/users/trashed', [UsersController::class, 'trashed']);
        Route::post('/users/{id}/restore', [UsersController::class, 'restore']);
        Route::delete('/users/{id}/force', [UsersController::class, 'forceDelete']);
        Route::get('/enquiries/trashed', [EnquiriesController::class, 'trashed']);
        Route::post('/enquiries/{id}/restore', [EnquiriesController::class, 'restore']);
        Route::delete('/enquiries/{id}/force', [EnquiriesController::class, 'forceDelete']);

    });

    Route::group(['middleware' => ['role:admin,user']], function () {
        Route::apiResource('/enquiries', EnquiriesController::class)->except(['store', 'create']);
    });
});