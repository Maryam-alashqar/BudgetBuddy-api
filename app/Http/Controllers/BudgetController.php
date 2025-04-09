<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use App\Models\Budget;
use App\Models\Expense;

class BudgetController extends Controller
{
 public function setBudget(Request $request)
{
    $user = auth()->user();

    $totalIncome = $user->jobs()->sum('salary_amount');

    // Check if custom percentages are provided
    $custom = $request->only(['needs_percentage', 'wants_percentage', 'savings_percentage']);

    if (array_filter($custom)) {
        // Validate custom input
        $request->validate([
            'needs_percentage' => 'required|numeric|min:0|max:100',
            'wants_percentage' => 'required|numeric|min:0|max:100',
            'savings_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $total = $request->needs_percentage + $request->wants_percentage + $request->savings_percentage;

        if ($total !== 100) {
            return response()->json(['error' => 'Custom percentages must add up to 100%'], 422);
        }

        $needsPercentage = $request->needs_percentage;
        $wantsPercentage = $request->wants_percentage;
        $savingsPercentage = $request->savings_percentage;
    } else {
        // Default 50/30/20 rule
        $needsPercentage = 50;
        $wantsPercentage = 30;
        $savingsPercentage = 20;
    }

    // Calculate actual money amounts from salary
    $needsAmount = $totalIncome * ($needsPercentage / 100);
    $wantsAmount = $totalIncome * ($wantsPercentage / 100);
    $savingsAmount = $totalIncome * ($savingsPercentage / 100);

    // Save the budget in the budget table (using the relationship between User and Budget)
    $user->budget()->create([
        'needs_percentage' => $needsPercentage,
        'wants_percentage' => $wantsPercentage,
        'savings_percentage' => $savingsPercentage,
        'needs_amount' => $needsAmount,
        'wants_amount' => $wantsAmount,
        'savings_amount' => $savingsAmount,
    ]);

    return response()->json([
        'message' => 'Budget set successfully',
        'Income amount' => $totalIncome,
        'budget' => [
            'needs' => $needsAmount,
            'wants' => $wantsAmount,
            'savings' => $savingsAmount,
        ]
    ]);
}


}
