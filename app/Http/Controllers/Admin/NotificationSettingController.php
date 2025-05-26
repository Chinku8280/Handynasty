<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationSettingController extends Controller
{
    public function update_setting(Request $request)
    {
        // return $request->all();

        $input_data = [
            'category_notif_status' => $request->input('category_notif_status') ?? 2,
            'service_notif_status' => $request->input('service_notif_status') ?? 2,
            'coupon_notif_status' => $request->input('coupon_notif_status') ?? 2,
            'voucher_notif_status' => $request->input('voucher_notif_status') ?? 2,
            'loyalty_shop_notif_status' => $request->input('loyalty_shop_notif_status') ?? 2,
            'promotion_notif_status' => $request->input('promotion_notif_status') ?? 2,
            'happening_notif_status' => $request->input('happening_notif_status') ?? 2,
        ];

        $notification_settings = DB::table('notification_settings')->first();

        if (!$notification_settings) 
        {
            DB::table('notification_settings')->insert($input_data);
        }
        else
        {
            DB::table('notification_settings')
                    ->where('id', $notification_settings->id)
                    ->update($input_data);
        }

        return response()->json(['status'=>'success', 'message'=>"Data Updated Succesfully"]);
    }

    public static function get_notification_settings($col)
    {
        $notification_settings = DB::table('notification_settings')->first();

        if($notification_settings)
        {
            if($notification_settings->$col == 1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}
