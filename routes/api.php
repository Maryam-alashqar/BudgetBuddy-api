<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyOtpController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmergencyFundController;



/**
 * Auth Controllers.
 */
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->post('/verify', [VerifyOtpController::class, 'verify']);


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

    // Add Other Job
    Route::post('/fixed-jobs', [JobController::class, 'storeFixedJob']);
    Route::post('/irregular-jobs', [JobController::class, 'storeIrregularJob']);
    Route::post('/update-fixed-jobs/{id}', [JobController::class, 'updateFixedJob']);
    Route::post('/update-irregular-jobs/{id}', [JobController::class, 'updateIrregularJob']);
    // Net balance
    Route::get('/account/balance', [HomeController::class, 'getNetBalance']);
    Route::post('/set-budget', [BudgetController::class, 'setBudget']);
    Route::get('/emergency-fund', [EmergencyFundController::class, 'show']);

    /**
    *   Add expenses, Predefined subcategories or manually.
    *   Show each expenses Category.
    *   Delete expenses by id.
    */
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses/{category}', [ExpenseController::class, 'getExpensesByCategory']);
    Route::delete('/expenses/{expenseId}', [ExpenseController::class, 'deleteExpenseById']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);


    /**
     *  Savings; add, show, edit and delete goals.
     */
    Route::post('/add-goal', [SavingController::class, 'addGoals']);
    Route::get('/get-goals', [SavingController::class, 'getGoals']);
    Route::put('/update-goal/{id}', [SavingController::class, 'updateGoal']);
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
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);


    //exist session
    Route::post('/logout', [LoginController::class, 'logout']);
});

// Privacy policy
Route::get('/privacy-policy', function () {
    return response()->json([
        'title' => 'Privacy Policy for BudgetBuddy V. 1.00',
        'content' => 'We respect your privacy. This Privacy Policy explains how we collect,
        use, and protect user data in the BudgetBuddy API.
        By using our services, you agree to the terms outlined below.
        1. Information We Collect:
            - User Identification (Name, Email)
            - Transaction Details
            - Device and Usage Data
        2. Usage:
            - To provide budgeting notifications and salary reminders
            - To improve API performance and reliability
        3. Data Security:
            - All data is encrypted and stored securely
            - API requests are authenticated using tokens
        4. Data Sharing:
            - We do not share user data with third parties
        5. User Rights:
            - Users can request to view, export, or delete their data
        6. Policy Updates:
            - We will notify users of any changes via app alerts or documentation updates.

        For any questions, contact us at: support@budgetbuddyapi.com'
    ]);
});
Route::post('/contact-us', [ContactUsController::class, 'send']);
