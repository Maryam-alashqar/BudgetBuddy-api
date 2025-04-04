<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Job;



class ProfileController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($userId)
    {
         $user = User::with('jobs')->findOrFail($userId);

         Log::info('User Data:', $user->toArray());

         return response()->json([
            'user' => $user->only(['name', 'email', 'phone_number']),
            'jobs' => $user->jobs->isEmpty() ? "No jobs found.."
            : $user->jobs->map(function ($job) {
                return $job->only(['job_sector', 'job_title', 'job_position', 'salary_amount']);

            })
        ], 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $userId)
{
    $user = User::with('jobs')->findOrFail($userId);

    // Get the first job (assuming one job per user, update for multiple jobs if needed)
    $job = $user->jobs()->first();

    // Pre-fill missing request data with existing values
    $request->merge([
        'name' => $request->input('name', $user->name),
        'email' => $request->input('email', $user->email),
        'phone_number' => $request->input('phone_number', $user->phone_number),
        'job_sector' => $request->input('job_sector', $job?->job_sector),
        'job_title' => $request->input('job_title', $job?->job_title),
        'job_position' => $request->input('job_position', $job?->job_position),
        'salary_amount' => $request->input('salary_amount', $job?->salary_amount),
    ]);

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,'.$user->id,
        'phone_number' => 'sometimes|string|max:15',
        'job_sector' => 'sometimes|string',
        'job_title' => 'nullable|string',
        'job_position' => 'nullable|string',
        'salary_amount' => 'sometimes|numeric',
        'password' => 'sometimes|string|min:8|confirmed',
    ]);

    // Update user info
    $userData = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone_number' => $validated['phone_number'],
    ];

    // Only update password if it was provided
    if ($request->filled('password')) {
        $userData['password'] = Hash::make($validated['password']);
    }

    $user->update($userData);

    // Job data
    $jobData = [
        'job_sector' => $validated['job_sector'],
        'job_title' => $validated['job_title'],
        'job_position' => $validated['job_position'],
        'salary_amount' => $validated['salary_amount'],
    ];

    // Update or create job info
    if ($user->jobs()->exists()) {
        $user->jobs()->update($jobData);
    } else {
        $user->jobs()->create($jobData);
    }

    // Reload the user with jobs relationship
    $user->load('jobs');

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user->only(['id', 'name', 'email', 'phone_number']),
        'jobs' => $user->jobs->map(function ($job) {
            return $job->only(['job_sector', 'job_title', 'job_position', 'salary_amount']);
        })
    ], 200);
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
