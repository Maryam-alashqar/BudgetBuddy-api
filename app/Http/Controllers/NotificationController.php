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
            'notifications' => $user->notifications //get all notifications.
    ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();
        $notification->update(['read' => true]);

        return response()->json(['message' => 'Notification marked as read']);
    }

}

