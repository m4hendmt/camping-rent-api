<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/payments', [PaymentController::class, 'index']);
Route::get('/payments/{id}', [PaymentController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['role:user,admin'])->group(function () {
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::put('/payments/{id}', [PaymentController::class, 'update']);
    });
    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);
    });
});
