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
 public function storeFixedJob(Request $request)
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

 public function storeIrregularJob(Request $request)
{
    $user = Auth::user();

    try {
        // Check for job-related input
        $hasJobData = $request->hasAny([
            'salary_amount','job_title',
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
            'job_title' => 'required|string|max:255',
        ]);


        return response()->json([
            'message' => 'New job added successfully',
            'job_title' => $job->job_title,
            'salary_amount' => $job->salary_amount,
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


 public function updateFixedJob(Request $request, $id)
{
    $user = auth()->user();
    $job = $user->jobs()->where('id', $id)->first();

    if (!$job) {
        return response()->json(['message' => 'Job not found'], 404);
    }

    // Pre-fill missing request data with existing job values
    $request->merge([
        'job_sector' => $request->input('job_sector', $job->job_sector),
        'job_title' => $request->input('job_title', $job->job_title),
        'job_position' => $request->input('job_position', $job->job_position),
        'salary_amount' => $request->input('salary_amount', $job->salary_amount),
    ]);

    $validated = $request->validate([
        'job_sector' => 'sometimes|string',
        'job_title' => 'nullable|string',
        'job_position' => 'nullable|string',
        'salary_amount' => 'sometimes|numeric',
    ]);

    $job->update($validated);

    // Return only the updated job
    return response()->json([
        'message' => 'Job updated successfully',
        'job' => $job->only(['id', 'job_sector', 'job_title', 'job_position', 'salary_amount']),
    ], 200);
}


public function updateIrregularJob(Request $request, $id)
{
    $user = auth()->user();
    $job = $user->jobs()->where('id', $id)->first();

    if (!$job) {
        return response()->json(['message' => 'Job not found'], 404);
    }

    // Pre-fill missing request data with existing values
    $request->merge([
        'job_title' => $request->input('job_title', $job->job_title),
        'salary_amount' => $request->input('salary_amount', $job->salary_amount),
    ]);

    $validated = $request->validate([
        'job_title' => 'nullable|string',
        'salary_amount' => 'sometimes|numeric',
    ]);

    $job->update($validated);

    // Return updated job
   return response()->json([
        'message' => 'Job updated successfully',
        'job' => $job->only(['id', 'job_title', 'salary_amount']),
    ], 200);
}


}
