<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bonuses;
use App\Models\User;

class BonusController extends Controller
{

    public function updateBonusPreference(Request $request)
    {
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
        'message' => 'Bonud preference updated successfully',
        'user' => $user,
        ]);

    }
     // only for fixed_Income users
    public function store(Request $request)
    {
        $request->validate([

            'amount' => 'required|numeric|min:0',
           'bonus_date' => 'required|regex:/^\d{2}-\d{2}$/',
            'is_permanent' => 'boolean'
        ]);

        $user = User::where('id', $request->user_id)->where('role', 'fixed_income')->first();

        if (!$user) {
            return response()->json(['error' => 'User is not a fixed income user'], 403);
        }

        $bonus = Bonuses::create($request->all());

        return response()->json(['message' => 'Bonus added successfully', 'bonus' => $bonus]);
    }
    public function index()
    {
        $bonuses = Bonuses::whereHas('user', function ($query) {
            $query->where('role', 'fixed_income');
        })->get();

        return response()->json(['bonuses' => $bonuses]);
    }

}
