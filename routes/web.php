<?php

use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AssignmentSubmissionController::class, 'index'])->name('dashboard');
    Route::post('/submissions', [AssignmentSubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}/download', [AssignmentSubmissionController::class, 'download'])
        ->name('submissions.download');
    Route::delete('/submissions/{submission}', [AssignmentSubmissionController::class, 'destroy'])
        ->name('submissions.destroy');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
