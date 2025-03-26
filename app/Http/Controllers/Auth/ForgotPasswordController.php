<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class ForgotPasswordController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    public function sendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Generate random OTP
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes
        $user->save();

        // Log OTP to check if it's generated
         Log::info("OTP for {$user->email}: $otp");

        // Send OTP via email
        Mail::raw("Your OTP for password reset is: $otp", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Password Reset OTP');
        });

        return response()->json(['message' => 'OTP sent to your email'], 200);
    }

}
