<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginUserRequest;


class AuthController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
  public function register(StoreUserRequest $request): JsonResponse
    {

        $validator = $request->validated();

        $user = User::create([
            'name' => $validator['name'],
            'email' => $validator['email'],
            'phone_number' => $validator['phone_number'],
            'salary_type' => $validator['salary_type'],
            'salary_type' => $validator['salary_type'] ?? 'fixed', // Default to "fixed"
            'password' => Hash::make($validator['password']),
        ]);

        $job = Job::create([
            'user_id' => $user->id,
            'salary_amount' => $validator['salary_amount'],
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

    /**
     * user login
     */

 public function login(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        // Verify credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }




    /**
     * User Log out from the session
     */
    public function logout(Request $request)
    {
    $request->user()->tokens()->delete(); // Revoke all tokens

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
}

}
