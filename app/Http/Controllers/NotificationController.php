<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Notifications\ItemRecalledNotification;
use App\Services\Utilities\FCMService;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    public function listUserNotifications(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(10); 

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }


    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

}
