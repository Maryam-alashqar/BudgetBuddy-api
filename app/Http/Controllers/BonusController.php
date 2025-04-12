<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bonuses;
use App\Models\User;

class BonusController extends Controller
{

    public function updateBonusPreference(Request $request)
    {    $user = Auth::user();

        $request->validate([
            'receives_bonus'=>'required|boolean',

        ]);

        $user = Auth::user();
         if ($user->role !== 'fixed_income') {
            return response()->json(['error' => 'Only fixed income user can set this preference'], 403);
        }

        $user->update([
            'receives_bonus' => $request->receives_bonus,
        ]);

        return response()->json([
        'message' => 'Bonus preference updated successfully',

        ]);

    }
     // only for fixed_Income users
    public function store(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:0',
        'bonus_date' => 'required|date',
        'is_permanent' => 'nullable|boolean'
    ]);

    // Find 'fixed_income' user
    $user = User::where('id', $request->user_id)->where('role', 'fixed_income')->first();

    // If user doesn't exist or isn't fixed_income.
    if (!$user) {
        return response()->json(['error' => 'User not found or not a fixed income user'], 403);
    }

    // Create the bonus and explicitly set the user_id
    $bonus = Bonuses::create([
        'amount' => $request->amount,
        'bonus_date' => $request->bonus_date,
        'is_permanent' => $request->is_permanent,
        'user_id' => $user->id,  // Explicitly set the user_id
    ]);

    return response()->json(['message' => 'Bonus added successfully', 'bonus' => $bonus]);
}


}
