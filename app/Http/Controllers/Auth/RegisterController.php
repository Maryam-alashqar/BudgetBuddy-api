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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    //
public function register(StoreUserRequest $request): JsonResponse
{
    try {
        $validated = $request->validated();

        $otp = rand(1000, 9999); // OTP من 4 أرقام
        $otpExpiry = Carbon::now()->addMinutes(10);


        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role' => $validated['role'] ?? 'fixed_income,irregular_income',
            'password' => Hash::make($validated['password']),
            'otp' => $otp,
            'otp_expires_at' => $otpExpiry,

        ]);

        $paydayInput = $validated['payday'] ?? null;
        if ($paydayInput) {
            $payday = Carbon::createFromFormat('d-m', $paydayInput)->setYear(now()->year);
        } else {
            $payday = now()->startOfMonth();
        }

        $job = Job::create([
            'user_id' => $user->id,
            'salary_amount' => $validated['salary_amount'],
            'payday' => $payday,
            'job_sector' => $validated['job_sector'],
            'job_title' => $validated['job_title'],
        ]);

        // Send OTP via email
        Mail::raw("Your OTP for verfication is: $otp", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify your email');
        });

        return response()->json([

              'message' => "{$user->email} registered successfully!",
            'data' => $user ,
            'job data' => $job,
        ], 201);


    } catch (\Exception $e) {
        Log::error('Registration error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

}
