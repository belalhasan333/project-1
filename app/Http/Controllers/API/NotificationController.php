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

        $allNotifications = $user->notifications()->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Recent notifications retrieved successfully.',
            'code' => 200,
            'data' => [
                'has_unread_notifications' => $user->unreadNotifications()->exists(),
                'notifications' => $allNotifications->map(fn($n) => [
                    'id' => $n->id,
                    'type' => $n->type,
                    'notifiable_type' => $n->notifiable_type,
                    'notifiable_id' => $n->notifiable_id,
                    'data' => $n->data,
                    'is_read' => $n->read_at ? true : false,
                    'read_at' => $n->read_at,
                    'created_at' => $n->created_at,
                    'updated_at' => $n->updated_at,
                ]),
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
            'body'  => 'You have a new notification',
            'url'   => url('/'),
        ];

        Notification::send($users, new UserNotification($data));

        return response()->json([
            'status' => true,
            'message' => 'Test notifications sent successfully',
        ], 200);
    }
}
