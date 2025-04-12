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
        // Check for job-related input
        $hasJobData = $request->hasAny([
            'salary_amount', 'job_sector', 'job_title', 'job_position', 'payday'
        ]);

        if (!$hasJobData) {
            return response()->json([
                'status' => 'success',
                'message' => 'No job information provided',
                'data' => null
            ], 200);
        }

        // Validate incoming data
        $validated = $request->validate([
            'salary_amount' => 'required|numeric|min:0',
            'job_sector' => 'required|in:Governmental,Private',
            'job_title' => 'required|string|max:255',
            'job_position' => 'nullable|string|max:255',
            'payday' => 'nullable|date',
        ]);

       // Set payday if provided or default if not provided in request
        $payday = $validated['payday'] ?? now()->startOfMonth();

        // Remove payday from job data before saving to prevent accidental assignment to users table
        $jobData = collect($validated)->except('payday')->toArray();

        // Create job for the user
        $job = $user->jobs()->create(array_merge($jobData, ['payday' => $payday]));


        return response()->json([
            'status' => 'success',
            'message' => 'New job added successfully',
            'salary_amount' => $job->salary_amount,
        'job_sector' => $job->job_sector,
        'job_title' => $job->job_title,
        'job_position' => $job->job_position,
        'payday' => $job->payday,
        ], 201);

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
