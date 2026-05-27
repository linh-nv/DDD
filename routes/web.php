<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamTakeController;

Route::get('/', function () {
    return view('welcome');
});

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/exam/{id}', [ExamTakeController::class, 'show'])->name('exam.show');
    Route::post('/exams/submit', [ExamController::class, 'submit'])->name('exam.submit');
    Route::post('/exams/submit-ddd', [ExamController::class, 'submit_ddd'])->name('exam.submit_ddd');
});
