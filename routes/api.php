<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\DetoxController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\MeditationController;
use App\Http\Controllers\Api\NotificationController;


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    // forget and reset
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);
    // profile
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
    // categories
    Route::apiResource('/categories', CategoryController::class);
    // favorite toggle
    Route::post('/favorite/toggle', [FavoriteController::class, 'toggle'])->middleware('auth:api');

    // item using by seeder for api
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items/{id}/favorite', [ItemController::class, 'toggleFavorite']);
    // notifications
    Route::get('v1/notifications', [NotificationController::class, 'index']);
    Route::post('v1/notifications/read/{id}', [NotificationController::class, 'markAsRead']);
    Route::post('v1/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('v1/notifications/{id}', [NotificationController::class, 'destroy']);
    // detox proggess
    Route::post('/detox/todayProgress', [DetoxController::class, 'updateProgress']);
    // profile
    Route::post('profile', [ProfileController::class, 'updateProfile']);
    // goals route
    Route::apiResource('/goals', GoalController::class);
    Route::post('/goals/{goal}/complete-today', [GoalController::class, 'completeToday']);
    Route::post('/goals/{goal}/achieve', [GoalController::class, 'achieve']);

    // maditation route
    Route::apiResource('/meditations', MeditationController::class);
});
