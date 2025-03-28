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
    // Predefined subcategories for each category
    private $Subcategories = [
        'primary_bill' => ['Electricity bill', 'Gas bill', 'Water bill',
        'Internet Subscription', 'Phone', 'rent', 'mortgage'],
        'tax' => ['Income_tax', 'Property_tax', 'Sales_tax', 'Vehicle_tax'],
        'need' => ['Groceries', 'Healthcare', 'Transportation', 'Education', 'Insurance'],
        'want' => ['Dining_out', 'Entertainment', 'Hobbies', 'Travel', 'Subscriptions']
    ];


    /**
     * Store a new expense
     */


    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'category' => [
                'required',
                Rule::in(array_keys($this->Subcategories))
            ],
            'subcategory' => 'required|string|max:255',
            'expenses_name' => 'nullable|string|max:255',
            'expenses_amount' => 'required|numeric|min:0.01',
            'is_custom' => 'sometimes|boolean'
        ]);

        $isCustom = $request->input('is_custom', false);

        if (!$isCustom) {
            $subcategories = array_map('strtolower', $this->Subcategories[$validatedData['category']]);
            if (!in_array(strtolower($validatedData['subcategory']), $subcategories)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid subcategory for selected category',
                    'valid_subcategories' => $this->Subcategories[$validatedData['category']]
                ], 422);
            }
        }

        // database transaction to ensure data consistency
        DB::transaction(function () use ($user, $validatedData, $isCustom) {

            $expense = $user->expenses()->create([
                'category' => $validatedData['category'],
                'subcategory' => $validatedData['subcategory'],
                'expenses_name' => $validatedData['expenses_name'] ?? null,
                'expenses_amount' => $validatedData['expenses_amount'],
                'is_custom_subcategory' => $isCustom
            ]);

            // Calculate total for all user's expenses
            $newTotal = $user->expenses()->sum('expenses_amount');

            // Update records with the new total
            $user->expenses()->update(['expenses_total' => $newTotal]);
             // Check if user has an expense limit set
            if ($user->expense_limit) {
                $percentage = ($newTotal / $user->expense_limit) * 100;

                // Check if reached or exceeded 80%
                if ($percentage >= 80) {
                    // Check if we haven't already notified for this threshold
                    $alreadyNotified = Notification::where('user_id', $user->id)
                        ->where('type', 'expense_limit')
                        ->where('created_at', '>=', now()->subDay())
                        ->exists();
                }
                if (!$alreadyNotified) {
                    Notification::create([
                            'user_id' => $user->id,
                            'type' => 'expense_limit',
                            'message' => "Your expenses have reached {$percentage}% of your limit!"
                        ]);
                    }
                        // Optional: Send push notification
                        $this->sendExpenseLimitNotification($user, $percentage);
          }

        });


        return response()->json([
            'status' => 'success',
            'message' => 'Expense added successfully'
        ], 201);

        }



     /**
     * Get subcategories for a specific category
     */
    public function getSubcategories($category)
    {
        if (!array_key_exists($category, $this->Subcategories)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid category'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'predefined' => $this->Subcategories[$category],
                'allow_custom' => true
            ]
        ]);
    }


     /**
     * to calculate a user's net account amount (total income minus total expenses
     */
  public function getNetBalance()
    {
        $user = Auth::user();
        try {
            // Calculate total income from all sources
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



    /**
 * Get all expenses in each category
 */
    public function getNeeds(Request $request)
    {
         $user = Auth::user();

         $needs = $user->expenses()->where('category', 'need')->latest()
         ->get();

         return response()->json([
         'status' => 'success',
         'count' => $needs->count(),
         'data' => $needs
        ]);
    }

    public function getWants(Request $request)
{
    $user = Auth::user();

    // Base query for needs
    $needs = $user->expenses()
                ->where('category', 'want')
                ->latest()
                ->get();

    return response()->json([
        'status' => 'success',
        'count' => $needs->count(),
        'data' => $needs
    ]);
}

    public function getBills(Request $request)
    {
         $user = Auth::user();

         $needs = $user->expenses()->where('category', 'primary_bill')->latest()
         ->get();

         return response()->json([
         'status' => 'success',
         'count' => $needs->count(),
         'data' => $needs
        ]);
    }

    public function getTaxes(Request $request)
    {
         $user = Auth::user();

         $needs = $user->expenses()->where('category', 'tax')->latest()
         ->get();

         return response()->json([
         'status' => 'success',
         'count' => $needs->count(),
         'data' => $needs
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
