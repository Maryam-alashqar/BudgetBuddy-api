<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\BonusController;



/**
 * Auth Controllers.
 */
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);


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
    Route::post('/jobs', [JobController::class, 'store']);
    // Net balance
    Route::get('/account/balance', [HomeController::class, 'getNetBalance']);


    /**
    *   Add expenses, Predefined subcategories or manually.
    *   Show each expenses Category.
    *   Delete expenses by id.
    */
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses/{category}', [ExpenseController::class, 'getExpensesByCategory']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'deleteExpenseById']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);

    /**
     *  Savings; add, show, edit and delete goals.
     */
    Route::post('/goal', [SavingController::class, 'addGoals']);
    Route::delete('/goal/{id}', [SavingController::class, 'deleteSavingsById']);

    // financial report
    Route::get('/bar-chart', [FinancialController::class, 'getFinancialReport']);
    Route::get('/pie-chart', [FinancialController::class, 'getExpensesPercentage']);

    /**
    * Special feature for fixed income users.
    * Add bonus info and get data.
    */
    Route::post('/set-bonus-preference', [BonusController::class, 'updateBonusPreference']);
    Route::post('/bonuses', [BonusController::class, 'store']);
    Route::get('/bonuses', [BonusController::class, 'index']);
    /**
    * Notification.
    */
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);


});
