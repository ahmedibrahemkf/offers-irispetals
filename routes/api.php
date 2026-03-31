<?php

use App\Http\Controllers\Api\DataController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [DataController::class, 'health']);

Route::get('/settings', [DataController::class, 'getSettings']);
Route::post('/settings', [DataController::class, 'saveSettings']);

Route::get('/orders', [DataController::class, 'listOrders']);
Route::post('/orders', [DataController::class, 'upsertOrder']);
Route::delete('/orders/{id}', [DataController::class, 'deleteOrder']);

Route::get('/expenses', [DataController::class, 'listExpenses']);
Route::post('/expenses', [DataController::class, 'upsertExpense']);
Route::delete('/expenses/{id}', [DataController::class, 'deleteExpense']);

Route::post('/upload', [DataController::class, 'uploadImage']);
