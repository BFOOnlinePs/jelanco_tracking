<?php

namespace App\Http\Controllers\ApiControllers\fcm_controllers;

use App\Http\Controllers\Controller;
use App\Models\NotificationModel;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUserNotifications(Request $request)
    {
        $notifications = NotificationModel::where('user_id', Auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return response()->json([
            'status' => true,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total_items' => $notifications->total(),
            ],
            'notifications' => $notifications->values(),
        ], 200);
    }


    // unread notifications count function
    public function unreadNotificationsCount()
    {
        $unread_notifications_count = NotificationModel::where('user_id', Auth()->user()->id)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'status' => true,
            'unread_notifications_count' => $unread_notifications_count
        ], 200);
    }

    public function readNotification($notification_id)
    {
        NotificationModel::where('id', $notification_id)
            ->update([
                'is_read' => 1
            ]);

        return response()->json([
            'status' => true,
        ], 200);
    }

    public function readAll()
    {
        NotificationModel::where('user_id', Auth()->user()->id)
            ->update([
                'is_read' => 1
            ]);

        return response()->json([
            'status' => true
        ], 200);
    }
}
