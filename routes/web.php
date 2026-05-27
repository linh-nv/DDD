<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamTakeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminExamController;
use App\Http\Controllers\Admin\AdminQuestionController;

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

// ── Admin routes ──────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Exams
        Route::resource('exams', AdminExamController::class)->except(['show']);
        Route::post('exams/{exam}/toggle-active', [AdminExamController::class, 'toggleActive'])->name('exams.toggle-active');
        Route::get('exams/{exam}/questions', [AdminExamController::class, 'questions'])->name('exams.questions');
        Route::post('exams/{exam}/questions/add', [AdminExamController::class, 'addQuestion'])->name('exams.questions.add');
        Route::delete('exams/{exam}/questions/{question}', [AdminExamController::class, 'removeQuestion'])->name('exams.questions.remove');
        Route::post('exams/{exam}/questions/reorder', [AdminExamController::class, 'reorderQuestions'])->name('exams.questions.reorder');

        // Questions
        Route::resource('questions', AdminQuestionController::class)->except(['show']);
    });
});

// ── User routes ───────────────────────────────────────────────────────────────
// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/exam/{id}', [ExamTakeController::class, 'show'])->name('exam.show');
    Route::post('/exams/submit', [ExamController::class, 'submit'])->name('exam.submit');
    Route::post('/exams/submit-ddd', [ExamController::class, 'submit_ddd'])->name('exam.submit_ddd');
});
