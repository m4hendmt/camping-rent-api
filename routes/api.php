<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\TransactionController;


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

Route::middleware('auth:api')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// Route yang bisa diakses user & admin (CRU: Create, Read, Update)
Route::middleware(['auth:api'])->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->whereNumber('id');
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::match(['put', 'patch'], '/transactions/{id}', [TransactionController::class, 'update'])->whereNumber('id');

    // Route khusus admin (D: Delete)
    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->whereNumber('id');
    });
});
