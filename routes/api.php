<?php

use App\Presentation\Http\Controllers\FamilyController;
use App\Presentation\Http\Controllers\TaskController;
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
});
