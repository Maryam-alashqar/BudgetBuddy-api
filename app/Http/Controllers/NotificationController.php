<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    // Send push notification & store it
    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:reminder,alert,update'
        ]);

        // Store notification in the database
        $notification = Notification::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type
        ]);

        // Send push notification (Firebase Cloud Messaging)
        $this->sendPushNotification($request->user_id, $request->title, $request->message);

        return response()->json(['message' => 'Notification sent successfully', 'notification' => $notification], 201);
    }

    // Fetch notifications for a user
    public function getUserNotifications($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    // Mark notification as read
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read' => true]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    // Delete a notification
    public function deleteNotification($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    // Helper function: Send push notification using Firebase
    private function sendPushNotification($userId, $title, $message)
    {
        $user = User::find($userId);
        if (!$user || !$user->device_token) {
            return;
        }

        $serverKey = env('FIREBASE_SERVER_KEY'); // Add Firebase key in .env

        $data = [
            'to' => $user->device_token,
            'notification' => [
                'title' => $title,
                'body' => $message
            ],
            'data' => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'type' => 'push'
            ]
        ];

        Http::withHeaders([
            'Authorization' => "key=$serverKey",
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $data);
    }
}

