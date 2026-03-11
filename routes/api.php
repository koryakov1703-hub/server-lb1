<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChangeLogController;

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
        Route::get('/{user}/story', [ChangeLogController::class, 'userStory'])->middleware('permission:get-story-user');
        Route::get('/', [UserRoleController::class, 'users'])->middleware('permission:get-list-user');
        Route::get('/{user}/role', [UserRoleController::class, 'index'])->middleware('permission:read-user');
        Route::post('/{user}/role', [UserRoleController::class, 'attach'])->middleware('permission:update-user');
        Route::delete('/{user}/role/{role}', [UserRoleController::class, 'destroy'])->middleware('permission:delete-user');
        Route::delete('/{user}/role/{role}/soft', [UserRoleController::class, 'softDelete'])->middleware('permission:delete-user');
        Route::post('/{user}/role/{role}/restore', [UserRoleController::class, 'restore'])->middleware('permission:restore-user');
    });

    Route::prefix('policy/role')->group(function () {
        Route::get('/{role}/story', [ChangeLogController::class, 'roleStory'])->middleware('permission:get-story-role');
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:get-list-role');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware('permission:read-role');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:create-role');
        Route::match(['put', 'patch'], '/{role}', [RoleController::class, 'update'])->middleware('permission:update-role');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-role');
        Route::delete('/{role}/soft', [RoleController::class, 'softDelete'])->middleware('permission:delete-role');
        Route::post('/{role}/restore', [RoleController::class, 'restore'])->middleware('permission:restore-role');
    });

    Route::prefix('policy/permission')->group(function () {
        Route::get('/{permission}/story', [ChangeLogController::class, 'permissionStory'])->middleware('permission:get-story-permission');
        Route::get('/', [PermissionController::class, 'index'])->middleware('permission:get-list-permission');
        Route::get('/{permission}', [PermissionController::class, 'show'])->middleware('permission:read-permission');
        Route::post('/', [PermissionController::class, 'store'])->middleware('permission:create-permission');
        Route::match(['put', 'patch'], '/{permission}', [PermissionController::class, 'update'])->middleware('permission:update-permission');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:delete-permission');
        Route::delete('/{permission}/soft', [PermissionController::class, 'softDelete'])->middleware('permission:delete-permission');
        Route::post('/{permission}/restore', [PermissionController::class, 'restore'])->middleware('permission:restore-permission');
    });

    Route::prefix('policy/role/{role}/permission')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->middleware('permission:read-role');
        Route::post('/', [RolePermissionController::class, 'attach'])->middleware('permission:update-role');
        Route::delete('/{permission}', [RolePermissionController::class, 'destroy'])->middleware('permission:delete-role');
        Route::delete('/{permission}/soft', [RolePermissionController::class, 'softDelete'])->middleware('permission:delete-role');
        Route::post('/{permission}/restore', [RolePermissionController::class, 'restore'])->middleware('permission:restore-role');
    });

});
