<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoyaltyPointSettingController extends Controller
{
    // update

    public function update(Request $request)
    {
        // return $request->all();

        $validator = Validator::make(
            $request->all(),
            [
                'loyalty_points_expired_days' => 'required',
            ],
            [],
            [
                'loyalty_points_expired_days' => 'Loyalty coin Expired Days',
            ]
        );

        if($validator->fails())
        {
            $error = $validator->errors();

            return response()->json(['status' => "error", 'error'=>$error]);
        }
        else
        {
            DB::table('loyalty_point_settings')->delete();
    
            $result = DB::table('loyalty_point_settings')->insert([
                'loyalty_points_expired_days' => $request->loyalty_points_expired_days,
            ]);
    
            if($result)
            {
                return response()->json(['status'=>'success', 'message'=>"Data updated succesfully"]);
            }
            else
            {
                return response()->json(['status'=>'failed', 'message'=>"Data is not updated"]);
            }
        }
    }
}
