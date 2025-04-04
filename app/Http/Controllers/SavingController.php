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

        $request->validate([
        'user_id' => 'required|exists:users,id',
        'saving_goal' => 'required|string|max:255|',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'saving_amount' => 'required|numeric|min:0',
        'note' => 'nullable|',
        'saving_total' => 'required|numeric|min:0',
    ]);

    Saving::create($request->all());

    return response()->json(['message' => 'Saving goal added successfully!']);
    }

    /**
     * Display a listing of the resource.
     */

    public function getGoals()
    {
        //
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
