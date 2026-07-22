<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\InspectionImageApiController;
use App\Http\Controllers\Api\ExamApiController;
use App\Http\Controllers\Api\VesselApiController;

// Public API Auth
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);

// Protected API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
    Route::get('/dashboard/chart-data', [DashboardApiController::class, 'chartData']);
    Route::get('/dashboard/recent-activity', [DashboardApiController::class, 'recentActivity']);

    // Inspection Images
    Route::get('/inspection-images', [InspectionImageApiController::class, 'index']);
    Route::get('/inspection-images/{id}', [InspectionImageApiController::class, 'show']);
    Route::post('/inspection-images', [InspectionImageApiController::class, 'store']);
    Route::delete('/inspection-images/{id}', [InspectionImageApiController::class, 'destroy']);

    // Exams
    Route::get('/exams', [ExamApiController::class, 'index']);
    Route::get('/exams/{id}', [ExamApiController::class, 'show']);
    Route::post('/exams', [ExamApiController::class, 'store']);

    // Vessels
    Route::get('/vessels', [VesselApiController::class, 'index']);
    Route::get('/vessels/{id}/inspections', [VesselApiController::class, 'inspections']);
    Route::get('/vessels/{id}/images', [VesselApiController::class, 'images']);
});
