<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;

class VerifyOtpController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|digits:4'
        ]);

        $user = Auth::user(); // المستخدم الحالي

        if (!$user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        if ($user->otp !== $request->otp) {
            return response()->json(['error' => 'Invalid OTP.'], 400);
        }

        if (Carbon::parse($user->otp_expires_at)->isPast()) {
            return response()->json(['error' => 'OTP has expired.'], 400);
        }

        // تحقق ناجح
        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully!'], 200);
    }

}


