<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //
    public function getNotifications()
    {
    $user = Auth::user();
    return response()->json([
        'notifications' => $user->notifications // استرجاع كل الإشعارات
    ]);
}
}
