<?php

use App\Http\Controllers\Api\DashboardStatsController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard/stats', DashboardStatsController::class)->name('api.dashboard.stats');
