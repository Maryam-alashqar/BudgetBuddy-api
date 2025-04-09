<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Job;
use App\Models\Expense;
use App\Models\Budget;

class ExpenseController extends Controller
{

    /**
     * Store a new expense
     */
 public function store(Request $request)
{
    $user = Auth::user();

    // Validate the expense input
    $validated = $request->validate([
        'category' => 'required|in:need,want,primary_bill,loan,tax',
        'expenses_name' => 'nullable|string|max:255',
        'deadline' => 'nullable|date',
        'expenses_amount' => 'required|numeric|min:0.01',
    ]);

    $category = $validated['category'];
    $amount = $validated['expenses_amount'];

    $totalIncome = $user->jobs()->sum('salary_amount');

    if (in_array($category, ['need', 'want'])) {
        // Fetch user's budget or fall back to default percentages
        $budget = $user->budget;

        // If no budget is set, use 50/30/20 rule
        $needsPercentage = $budget->needs_percentage ?? 50;
        $wantsPercentage = $budget->wants_percentage ?? 30;

        $needsAmount = $budget->needs_amount ?? ($totalIncome * ($needsPercentage / 100));
        $wantsAmount = $budget->wants_amount ?? ($totalIncome * ($wantsPercentage / 100));

        // Determine which category is being spent from
        $used = $user->expenses()->where('category', $category)->sum('expenses_amount');

        $limit = $category === 'need' ? $needsAmount : $wantsAmount;
        $remaining = $limit - $used;

        if ($totalIncome <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please set your salary first.'
            ], 400);
        }

        if ($remaining < $amount) {
            return response()->json([
                'status' => 'error',
                'message' => ucfirst($category) . ' budget limit exceeded. Remaining: ' . number_format($remaining, 2)
            ], 422);
        }
    }

    // Save the expense
    DB::transaction(function () use ($user, $validated) {
        $user->expenses()->create([
            'category' => $validated['category'],
            'deadline' => $validated['deadline'] ?? null,
            'expenses_name' => $validated['expenses_name'] ?? null,
            'expenses_amount' => $validated['expenses_amount'],
        ]);

        $newTotal = $user->expenses()->sum('expenses_amount');
        $this->notifyIfLimitReached($user, $newTotal);
    });

    return response()->json([
        'status' => 'success',
        'message' => 'Expense added successfully'
    ], 201);
}





private function notifyIfLimitReached($user, $total)
{
    if (!$user->expense_limit) return;

    $percentage = ($total / $user->expense_limit) * 100;

    if ($percentage >= 80) {
        $alreadyNotified = Notification::where('user_id', $user->id)
            ->where('type', 'expense_limit')
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if (!$alreadyNotified) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'expense_limit',
                'message' => "Your expenses have reached {$percentage}% of your limit!"
            ]);

            $user->notify(new ExpenseLimitReached($percentage));
        }
    }
}


   public function update(Request $request, $id)
    {
        $user = Auth::user();

        $expense = Expense::where('id', $id)->where('user_id', $user->id)->first();

        if (!$expense) {
            return response()->json([
                'status' => 'error',
                'message' => 'Expense not found or unauthorized'
            ], 404);
        }

        $validatedData = $request->validate([
            'category' => 'nullable|string|in:need,want,primary_bill,tax',
            'expenses_name' => 'nullable|string|max:255',
            'expenses_amount' => 'nullable|numeric|min:0.01',
            'deadline' => 'nullable|date',
        ]);

        $expense->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Expense updated successfully',
            'data' => $expense
        ], 200);
    }

    /**
     * Get all expenses in each category
     */

     public function getExpensesByCategory(Request $request, $category)
     {
        $user = Auth::user();
        $expenses = $user->expenses()
        ->where('category', $category)
        ->latest()
        ->get();


        return response()->json([
            'status' => 'success',
            'count' => $expenses->count(),
            'data' => $expenses
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */

     protected function deleteExpenseById($expenseId)
    {
        $expense = Expense::find($expenseId);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully'], 200);
    }
}
