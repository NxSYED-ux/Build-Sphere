<?php

namespace App\Http\Controllers\GeneralControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        return view('notifications');
    }

    public function getUnreadNotificationsCount(Request $request)
    {
        try {
            return response()->json([
                'count' => $request->user()->unreadNotifications()->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch unread notifications count', 'details' => $e->getMessage()], 500);
        }
    }

    public function getNotifications(Request $request)
    {
        try {
            return response()->json([
                'notifications' => $request->user()->notifications()->orderBy('created_at', 'desc')->get(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch notifications', 'details' => $e->getMessage()], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        try {

            $request->user()->notifications()
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['message' => 'All notifications marked as read']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to mark notifications as read', 'details' => $e->getMessage()], 500);
        }
    }

    public function markAsReadSingle(Request $request)
    {
        try {
            $request->validate([
                'notification_id' => 'required|uuid',
            ]);

            $notification = $request->user()->notifications()->find($request->notification_id);

            if (!$notification) {
                return response()->json(['error' => 'Notification not found'], 404);
            }

            if ($notification->read_at) {
                return response()->json(['message' => 'Notification is already read']);
            }

            $notification->update(['read_at' => now()]);

            return response()->json(['message' => 'Notification marked as read']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to mark notification as read', 'details' => $e->getMessage()], 500);
        }
    }
}
