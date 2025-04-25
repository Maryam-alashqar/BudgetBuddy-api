<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Job;



class ProfileController extends Controller
{

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


}
