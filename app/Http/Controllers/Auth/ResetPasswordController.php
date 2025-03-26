<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid OTP or email'], 400);
        }

        // Check if OTP is expired
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['error' => 'OTP expired'], 400);
        }

        // Reset password
        $user->password = Hash::make($request->password);
        $user->otp = null; // Clear OTP
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Password reset successful'], 200);
    }


}
