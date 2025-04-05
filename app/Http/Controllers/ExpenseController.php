<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Job;
use App\Models\Expense;

class ExpenseController extends Controller
{

    /**
     * Store a new expense
     */
 public function store(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'category' => 'required|string|max:255',
        'expenses_name' => 'nullable|string|max:255',
        'deadline' => 'nullable|date',
        'expenses_amount' => 'required|numeric|min:0.01',
    ]);

    DB::transaction(function () use ($user, $validated) {

        // Create expense with mass assignment
        $expense = $user->expenses()->create([
            'category' => $validated['category'],
            'deadline' => $validated['deadline'] ?? null,
            'expenses_name' => $validated['expenses_name'] ?? null,
            'expenses_amount' => $validated['expenses_amount'],
        ]);

        // Recalculate total and update all rows (if you're updating a summary field)
        $newTotal = $user->expenses()->sum('expenses_amount');

        $user->expenses()->update(['expenses_total' => $newTotal]);

        // Expense limit reached notification
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
