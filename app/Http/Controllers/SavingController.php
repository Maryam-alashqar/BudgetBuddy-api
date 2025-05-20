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
            'saving_goal' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'saving_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $saving = Saving::create($request->only([
            'saving_goal', 'start_date', 'end_date', 'saving_amount', 'note', 'user_id'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Saving goal added successfully!',
            'data' => $saving
        ]);
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


    public function updateGoal(Request $request, $id)
    {
        $user = Auth::user();

        $saving = Saving::where('id', $id)->where('user_id', $user->id)->first();

        if (!$saving) {
            return response()->json([
                'status' => 'error',
                'message' => 'Goal not found or unauthorized'
            ], 404);
        }

        $validatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'saving_amount' => 'required|numeric|min:0',
        ]);

        $saving->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Goal updated successfully',
            'data' => $saving
        ], 200);
    }

     protected function deleteSavingsById($savingId)
    {
        $saving = Saving::find($savingId);

        if (!$saving) {
            return response()->json(['message' => 'Goal not found'], 404);
        }

        $saving->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Goal deleted successfully'], 200);
    }
}
