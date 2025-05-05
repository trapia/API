<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->get();

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->update(['status' => 'read']);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function sendNotification(Request $request)
    {
        $notification = Notification::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'message' => $request->message,
            'status' => 'unread',
        ]);

        return response()->json($notification);
    }
}
