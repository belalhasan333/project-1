<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    // Get all notifications + unread count
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'is_read' => $notification->read_at ? true : false,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Recent notifications retrieved successfully.',
            'code' => 200,
            'data' => [
                'has_unread_notifications' => $user->unreadNotifications()->exists(),
                'notifications' => $user->notifications()
                    ->latest()
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'type' => $notification->type,
                            'notifiable_type' => $notification->notifiable_type,
                            'notifiable_id' => $notification->notifiable_id,
                            'data' => $notification->data,
                            'read_at' => $notification->read_at,
                            'created_at' => $notification->created_at,
                            'updated_at' => $notification->updated_at,
                        ];
                    }),
            ],
        ], 200);
    }

    // Mark single notification as read
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read',
        ], 200);
    }

    // Mark all notifications as read
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read',
        ], 200);
    }

    // Delete notification
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notification deleted successfully',
        ], 200);
    }

    // Send test notification to all users
    public function sendTestNotification()
    {
        $users = User::all();

        $data = [
            'title' => 'New Content Uploaded',
            'message' => 'You have a new notification',
            'url' => url('/'),
        ];

        Notification::send($users, new UserNotification($data));

        return response()->json([
            'status' => true,
            'message' => 'Test notifications sent successfully',
        ], 200);
    }
}
