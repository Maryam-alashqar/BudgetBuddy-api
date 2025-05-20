<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;

class EmergencyFundController extends Controller
{
     public function show(Request $request)
    {
        $user = Auth::user();

        $totalIncome = $user->jobs()->sum('salary_amount');
        $percentage = 0.2;
        $amount = $totalIncome * $percentage;

        return response()->json([
            'status' => 'success',
            'recommended_saving' => round($amount, 2),
            'note' => "Since your total income is $totalIncome.
             We advise you with a simple Emergency fund of $$amount. Ready to get started?"
        ]);
    }
}
