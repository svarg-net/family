<?php

use App\Presentation\Http\Controllers\AccountController;
use App\Presentation\Http\Controllers\FamilyController;
use App\Presentation\Http\Controllers\TaskController;
use App\Presentation\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);

    Route::post('/families', [FamilyController::class, 'store']);
    Route::put('/families/{id}', [FamilyController::class, 'update']);
    Route::post('/families/join', [FamilyController::class, 'join']);
    Route::delete('/families/{family}/members/{user}', [FamilyController::class, 'removeMember']);

    Route::get('/accounts', [AccountController::class, 'index']);
    Route::get('/accounts/{id}', [AccountController::class, 'show']);
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy']);
    Route::put('/accounts/{id}', [AccountController::class, 'update']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
});
