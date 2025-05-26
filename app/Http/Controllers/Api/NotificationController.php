<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // get_all_notification

    public function get_all_notification()
    {
        $user_id = Auth::user()->id;

        $notifications = auth()->user()->notifications()->get();

        auth()->user()->unreadNotifications->markAsRead();

        $result = [];

        foreach($notifications as $item)
        {
            $result[] = [
                'id' => $item->id,
                'title' => $item->data['title'],
                'body' => $item->data['body'],
                'upload_date' => date('d-m-Y', strtotime($item->data['upload_date'])),
                'target_page' => $item->data['target_page'],
                'created_at' => date('d-m-Y h:i:s A', strtotime($item->created_at))
            ];
        }

        if(!empty($result))
        {
            return response()->json([
                'status' => true,
                'message' => 'Notification list',
                'data' => $result
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found',
                'data' => $result
            ]);
        }
    }
}
