<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\InspectionImageController;
use App\Http\Controllers\VesselController;
use App\Http\Controllers\UserController;

// Auth Routes
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('check.api.auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/summary-statistics', [DashboardController::class, 'getSummaryStatistics'])->name('dashboard.summary');
    Route::get('/dashboard/debug-answers', [DashboardController::class, 'debugAnswers'])->name('dashboard.debug');

    // Inspection Images
    Route::prefix('images')->name('images.')->group(function () {
        Route::get('/', [InspectionImageController::class, 'index'])->name('index');
        Route::get('/data/{id}', [InspectionImageController::class, 'getImageData'])->name('data');
        Route::get('/{id}', [InspectionImageController::class, 'show'])->name('show');
        Route::delete('/{id}', [InspectionImageController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [InspectionImageController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [InspectionImageController::class, 'exportPdf'])->name('export.pdf');
               // ============ ADD DOWNLOAD ROUTE ============
        Route::get('/{id}/download', [InspectionImageController::class, 'download'])->name('download');
    });

    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/{id}', [ExamController::class, 'show'])->name('show');
        Route::get('/{examId}/questionnaire', [ExamController::class, 'questionnaire'])->name('questionnaire');
        Route::post('/{examId}/questionnaire', [ExamController::class, 'saveQuestionnaire'])->name('save-questionnaire');
        Route::get('/{examId}/report', [ExamController::class, 'report'])->name('report');
        Route::get('/export/excel', [ExamController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [ExamController::class, 'exportPdf'])->name('export.pdf');
        Route::post('/import', [ExamController::class, 'import'])->name('import');
    });

    // Vessels
    Route::prefix('vessels')->name('vessels.')->group(function () {
        Route::get('/', [VesselController::class, 'index'])->name('index');
        Route::get('/{vesselName}', [VesselController::class, 'show'])->name('show');
        Route::get('/export/excel', [VesselController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [VesselController::class, 'exportPdf'])->name('export.pdf');
    });

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/export/excel', [UserController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [UserController::class, 'exportPdf'])->name('export.pdf');
        
        // ================= CREATE USER (NEW ROUTES) =================
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        
        // Edit & Update User
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        
        // Update Password
        Route::get('/{id}/edit-password', [UserController::class, 'editPassword'])->name('edit-password');
        Route::put('/{id}/password', [UserController::class, 'updatePassword'])->name('update-password');
        
        // Delete User
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
});