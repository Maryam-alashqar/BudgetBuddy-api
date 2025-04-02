<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
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
