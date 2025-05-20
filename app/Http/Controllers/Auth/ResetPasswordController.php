<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    public function verifyOTPForReset(Request $request): JsonResponse
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:4'
    ]);

    $user = User::where('email', $request->email)->where('otp', $request->otp)->first();

    if (!$user) {
        return response()->json(['error' => 'Invalid OTP or email'], 400);
    }

    if (Carbon::now()->greaterThan($user->otp_expires_at)) {
        return response()->json(['error' => 'OTP has expired.'], 400);
    }


    $user->otp_verified_for_reset= true;
    $user->save();

    return response()->json(['message' => 'OTP verified successfully'], 200);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !$user->otp_verified_for_reset) {
        return response()->json(['error' => 'OTP verification required first.'], 403);
    }

    $user->password = Hash::make($request->password);
    $user->otp = null;
    $user->otp_expires_at = null;
    $user->otp_verified_for_reset = false; // Reset the flag
    $user->save();

    return response()->json(['message' => 'Password reset successful'], 200);
}


}
