<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\FinancialController;



/**
 * Auth Controllers.
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


/**
 * Forget Password with OTP.
 */
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOTP']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

/**
 * Show, edit, Update profile.
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile/{user}', [ProfileController::class, 'show']);
    Route::put('/profile/{user}', [ProfileController::class, 'update']);
    // Add Other Job
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    // Net balance
    Route::get('/account/balance', [ExpenseController::class, 'getNetBalance']);
    // Add expenses, Predefined subcategories or manually
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses/categories/{category}/subcategories', [ExpenseController::class, 'getSubcategories']);
    Route::get('/expenses/needs', [ExpenseController::class, 'getNeeds']);
    Route::get('/expenses/wants', [ExpenseController::class, 'getWants']);
    Route::get('/expenses/bills', [ExpenseController::class, 'getBills']);
    Route::get('/expenses/taxes', [ExpenseController::class, 'getTaxes']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'deleteExpenseById']);

    // financial report
    Route::get('/bar-chart', [FinancialController::class, 'getFinancialReport']);

});




/**
 * Notification.
 */
Route::post('/notifications/send', [NotificationController::class, 'sendNotification']);
Route::get('/notifications/{userId}', [NotificationController::class, 'getUserNotifications']);
Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
Route::delete('/notifications/{id}', [NotificationController::class, 'deleteNotification']);

