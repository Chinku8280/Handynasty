<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\NotificationDetail;
use App\Voucher;
use App\voucherItem;
use App\Coupon;
use App\CouponUser;
use App\Http\Controllers\Controller;
use App\Notifications\CategoryNotification;
use App\Notifications\CouponNotification;
use App\Notifications\ServiceNotification;
use App\Notifications\VoucherNotification;
use Berkayk\OneSignal\OneSignalFacade as OneSignal;
use Illuminate\Notifications\Notification as ClassNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    // for sending voucher notification

    public function sendVoucherNotification(Request $request)
    {
        $voucherId = $request->input('voucher_id');

        $voucher = Voucher::find($voucherId);

        $notification = new NotificationDetail([
            'title' => 'New Voucher Added',
            'body' => 'A new voucher is added: ' . $voucher->title,
            'send_push_notification' => true,
        ]);

        $notification->save();

        // if ($notification->send_push_notification) {
        //     LaravelNotification::send($users, $notification);
        // }

        // return response()->json(['message' => 'Notification sent successfully']);
        return response()->json(['message' => __('messages.notificationSentSuccessfully')]);
    }

    // for sending coupon notification

    public function sendCouponNotification(Request $request)
    { 
        $couponId = $request->input('coupon_id');

        $coupon = Coupon::find($couponId);

        $notification = new NotificationDetail([
            'title' => 'New Coupon Added',
            'body' => 'A new Coupon is added: ' . $coupon->coupon_code,
            'send_push_notification' => true,
        ]);

        $notification->save();

        return response()->json(['message' => __('messages.notificationSentSuccessfully')]);
    }


    // send notification

    public static function send_notification(Request $request, $customer, $module_id, $module_col_name, $module_name)
    {
        $validator = Validator::make($request->all(), [
            'notif_title' => 'required|string',
            'notif_body' => 'required|string',
            'upload_date' => 'required|date',
            'target_page' => 'nullable|string',
        ]);

        if ($validator->fails()) 
        {
            $error = $validator->errors();

            return response()->json(['status' => "error", 'error'=>$error]);
        }

        $flag = false;

        // onesignal push notification start

        foreach($customer as $item)
        {
            if(!empty($item->player_id))
            {
                OneSignal::sendNotificationToUser(
                    $request->notif_body,
                    $item->player_id,
                    $url = $request->target_page ?? null,
                    $data = [
                        'app_name' => 'Handynasty',  // Custom data field
                        'target_page' => $request->target_page ?? null
                    ],
                    $buttons = null,                             
                    $schedule = null,
                    $headings = $request->notif_title,
                    // $subtitle = null
                );
                                       
                $flag = true;
            }
        }

        // onesignal push notification end

        if($flag == true)
        {

            // normal notfication start

            $input_notification = [
                $module_col_name => $module_id,
                'title' => $request->notif_title,
                'body' => $request->notif_body,
                'upload_date' => date('Y-m-d', strtotime($request->upload_date)),
                'target_page' => $request->target_page ?? ''
            ];

            if($module_name == "category")
            {
                Notification::send($customer, new CategoryNotification($input_notification));
            }
            else if($module_name == "service")
            {
                Notification::send($customer, new ServiceNotification($input_notification));
            }
            else if($module_name == "coupon")
            {
                Notification::send($customer, new CouponNotification($input_notification));
            }
            else if($module_name == "voucher")
            {
                Notification::send($customer, new VoucherNotification($input_notification));
            }

            // normal notification end          

            $notification = new NotificationDetail();
            $notification->title = $request->notif_title;
            $notification->body = $request->notif_body;
            $notification->upload_date = date('Y-m-d', strtotime($request->upload_date));
            $notification->target_page = $request->target_page ?? '';
            $notification->module_name = $module_name;
            $notification->module_col_name = $module_col_name;
            $notification->module_id = $module_id;
            $notification->created_by = Auth::user()->id;
            $notification->save();

            return response()->json(['status' => 'success', 'message' => "Notification send successfully"]);
            
        }
        else
        {
            return response()->json(['status' => 'failed', 'message' => "Player Id is required to send notification"]);
        }
    }

}
