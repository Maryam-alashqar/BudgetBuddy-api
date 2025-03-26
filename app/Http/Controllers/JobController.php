<?php

namespace App\Http\Controllers;


use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    //
    /**
     * Store or update user's job information
     */
  public function store(Request $request)
{
    $user = Auth::user();

    try {
        // Only validate if request has any job data
        if ($request->hasAny(['salary_amount', 'job_sector', 'job_title', 'job_position'])) {
            $validatedData = $request->validate([
                'salary_amount' => 'required|numeric|min:0',
                'job_sector' => 'required|in:Governmental,Private',
                'job_title' => 'required|string|max:255',
                'job_position' => 'nullable|string|max:255',
            ]);

            // Create new job (don't update existing)
            $job = $user->jobs()->create($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'New job added successfully',
                'data' => $job
            ], 201);
        }

        // No job data provided
        return response()->json([
            'status' => 'success',
            'message' => 'No job information provided',
            'data' => null
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to add new job',
            'error' => $e->getMessage()
        ], 500);
    }
}
}

