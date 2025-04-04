<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Job;
use App\Models\Expense;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

      /**
     * to calculate a user's net account amount (total income minus total expenses
     */

  public function getNetBalance()
    {
        $user = Auth::user();
        try {
            // Calculate total income
            $totalIncome = $user->jobs()->sum('salary_amount');
            // Calculate total expenses
            $totalExpenses = $user->expenses()->sum('expenses_amount');

            // Calculate net balance
            $netBalance = $totalIncome - $totalExpenses;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_income' => $totalIncome,
                    'total_expenses' => $totalExpenses,
                    'net_balance' => $netBalance,
                    'currency' => $user->currency_preference ?? 'USD'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate balance',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
