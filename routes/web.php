<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\InspectionImageController;
use App\Http\Controllers\VesselController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DrillController;

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
        Route::get('/{id}/raw', [InspectionImageController::class, 'raw'])->name('raw');
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
        Route::get('/export/excel', [ExamController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [ExamController::class, 'exportPdf'])->name('export.pdf');
        Route::post('/import', [ExamController::class, 'import'])->name('import');
        Route::get('/{id}', [ExamController::class, 'show'])->name('show');

        // Review actions are shore (admin) only.
        Route::middleware('require.shore.role')->group(function () {
            Route::post('/batch/{batch}/approve', [ExamController::class, 'approveBatch'])->name('batch.approve');
            Route::post('/batch/{batch}/reject', [ExamController::class, 'rejectBatch'])->name('batch.reject');
            Route::post('/{id}/approve', [ExamController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [ExamController::class, 'reject'])->name('reject');
        });
    });

    // Vessels
    Route::prefix('vessels')->name('vessels.')->group(function () {
        Route::get('/', [VesselController::class, 'index'])->name('index');
        Route::get('/{vesselName}', [VesselController::class, 'show'])->name('show');
        Route::get('/export/excel', [VesselController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [VesselController::class, 'exportPdf'])->name('export.pdf');
    });

    // SIRE Readiness Drill — take the web quiz + shore results view.
    Route::prefix('drill')->name('drill.')->group(function () {
        Route::get('/', [DrillController::class, 'take'])->name('take');
        Route::post('/submit', [DrillController::class, 'submit'])->name('submit');
        Route::get('/results', [DrillController::class, 'results'])->name('results');
        Route::get('/results/{id}', [DrillController::class, 'show'])->name('show');
    });

    // Users — shore (admin) accounts only.
    Route::prefix('users')->name('users.')->middleware('require.shore.role')->group(function () {
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