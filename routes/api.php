<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\DetoxController;
use App\Http\Controllers\API\NotificationController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    // forget r reset
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);
    // profile
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');

    // item using by seeder for api
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items/{id}/favorite', [ItemController::class, 'toggleFavorite']);
    // notifications
    Route::get('v1/notifications', [NotificationController::class, 'index']);
    Route::post('v1/notifications/read/{id}', [NotificationController::class, 'markAsRead']);
    Route::post('v1/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('v1/notifications/{id}', [NotificationController::class, 'destroy']);
    // detox proggess
    Route::get('/detox/status', [DetoxController::class, 'status']);
    Route::post('/detox/start', [DetoxController::class, 'start']);
    Route::post('/detox/end',   [DetoxController::class, 'end']);
});
