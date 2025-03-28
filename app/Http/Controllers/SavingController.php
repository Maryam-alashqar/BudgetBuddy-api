<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Saving;
use App\Models\Job;

class SavingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getGoals()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addGoals(Request $request)
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
