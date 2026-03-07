<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth.token')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/out', [AuthController::class, 'out']);
        Route::get('/tokens', [AuthController::class, 'tokens']);
        Route::post('/out_all', [AuthController::class, 'outAll']);
    });
});

Route::prefix('ref')->middleware('auth.token')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserRoleController::class, 'users']);
        Route::get('/{user}/role', [UserRoleController::class, 'index']);
        Route::post('/{user}/role', [UserRoleController::class, 'attach']);
        Route::delete('/{user}/role/{role}', [UserRoleController::class, 'destroy']);
        Route::delete('/{user}/role/{role}/soft', [UserRoleController::class, 'softDelete']);
        Route::post('/{user}/role/{role}/restore', [UserRoleController::class, 'restore']);
    });

    Route::prefix('policy/role')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/{role}', [RoleController::class, 'show']);
        Route::post('/', [RoleController::class, 'store']);
        Route::match(['put', 'patch'], '/{role}', [RoleController::class, 'update']);
        Route::delete('/{role}', [RoleController::class, 'destroy']);
        Route::delete('/{role}/soft', [RoleController::class, 'softDelete']);
        Route::post('/{role}/restore', [RoleController::class, 'restore']);
    });

    Route::prefix('policy/permission')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('/{permission}', [PermissionController::class, 'show']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::match(['put', 'patch'], '/{permission}', [PermissionController::class, 'update']);
        Route::delete('/{permission}', [PermissionController::class, 'destroy']);
        Route::delete('/{permission}/soft', [PermissionController::class, 'softDelete']);
        Route::post('/{permission}/restore', [PermissionController::class, 'restore']);
    });

    Route::prefix('policy/role/{role}/permission')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index']);
        Route::post('/', [RolePermissionController::class, 'attach']);
        Route::delete('/{permission}', [RolePermissionController::class, 'destroy']);
        Route::delete('/{permission}/soft', [RolePermissionController::class, 'softDelete']);
        Route::post('/{permission}/restore', [RolePermissionController::class, 'restore']);
    });
});
