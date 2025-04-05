<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Saving;
use App\Models\Job;
use App\Rules\SavingNameRequired;

class SavingController extends Controller
{

      /**
     * Show the form for creating a new resource.
     */
    public function addGoals(Request $request)
    {
        $request->merge(['user_id' => auth()->id()]); // Add user_id to the request


        $request->validate([
        'saving_goal' => 'required|string|max:255|',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'saving_amount' => 'required|numeric|min:0',
        'note' => 'nullable|',
    ]);

     Saving::create($request->all());


    return response()->json(['message' => 'Saving goal added successfully!']);
    }

    /**
     * Display a listing of the resource.
     */

    public function getGoals()
    {
        $user = Auth::user();
        $savings = $user->savings()->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $savings
        ]);
    }




     protected function deleteSavingsById($savingId)
    {
        $saving = Saving::find($savingId);

        if (!$saving) {
            return response()->json(['message' => 'Goal not found'], 404);
        }

        $saving->delete();

        return response()->json(['message' => 'Goal deleted successfully'], 200);
    }
}
