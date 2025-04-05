<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Models\Job;

class RegisterController extends Controller
{
    //
    public function register(StoreUserRequest $request): JsonResponse
    {

        $validator = $request->validated();

        $user = User::create([
            'name' => $validator['name'],
            'email' => $validator['email'],
            'phone_number' => $validator['phone_number'],
            'role' => $validator['role'] ?? 'fixed_income',
            'password' => Hash::make($validator['password']),
        ]);
    $payday = $validated['payday'] ?? now()->startOfMonth();

        $job = Job::create([
            $payday = $validated['payday'] ?? now()->startOfMonth(),
            'user_id' => $user->id,
            'salary_amount' => $validator['salary_amount'],
            'payday' => $validator['payday'], // Insert payday into the jobs table
            'job_sector' => $validator['job_sector'],
            'job_title' => $validator['job_title'],
            'job_position' => $validator['job_position'],
        ]);

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
            'job' => $job,
        ], 201);

    }
}
