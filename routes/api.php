<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);        // открытый
    Route::post('/register', [AuthController::class, 'register']);  // только неавторизованные (добавим позже)
    Route::get('/me', [AuthController::class, 'me']);               // только авторизованные (добавим позже)
    Route::post('/out', [AuthController::class, 'out']);            // только авторизованные (добавим позже)
    Route::get('/tokens', [AuthController::class, 'tokens']);       // только авторизованные (добавим позже)
    Route::post('/out_all', [AuthController::class, 'outAll']);     // только авторизованные (добавим позже)
    Route::post('/refresh', [AuthController::class, 'refresh']);    // по refresh токену (добавим позже)
});
