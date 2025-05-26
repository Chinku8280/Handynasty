<?php

namespace App\Http\Controllers\Api;

use App\Coupon;
use App\CouponUser;
use App\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function my_coupons(Request $request)
    {
        $user_id = Auth::user()->id;
    
        $coupons = Coupon::where('status', 'active')->get();
        // dd($coupons);
        if (!$coupons->isEmpty()) {
            $userRedeemedCoupons = DB::table('coupon_users')
                ->where('user_id', $user_id)
                ->pluck('coupon_id')
                ->toArray();
    
            $couponsData = [];
    
            foreach ($coupons as $coupon) {
                $isUsed = in_array($coupon->id, $userRedeemedCoupons);
                $couponData = $coupon->toArray();
                $couponData['is_used'] = $isUsed;
                $couponsData[] = $couponData;
            }
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $couponsData,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => [],
            ]);
        }
    }    

    public function single_coupon($id)
    {
        $coupon = Coupon::find($id);
    
        if ($coupon) {

            $coupon->description = strip_tags($coupon->description);

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $coupon
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Coupon not found',
                'data' => null
            ]);
        }
    }   
    
    public function redeem_coupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);
    
        $user = Auth::user();
    
        // Finding the coupon by code

        $coupon = Coupon::where('id', $request->input('coupon_code'))
                        ->where('status', 'active')
                        ->first();
    
        if ($coupon) 
        {
            $userRedeemed = DB::table('coupon_users')
                ->where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->exists();
    
            if (!$userRedeemed) 
            {
                $couponUser = DB::table('coupon_users')->insertGetId([
                    'coupon_id' => $coupon->id,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Coupon redeemed successfully',
                    'data' => [
                        'coupon_user_id' => $couponUser,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);

            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Coupon already redeemed by the user',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired coupon code',
            ]);
        }
    }

    // public function redeemable_coupon(Request $request)
    // {
    //     $user_id = Auth::user()->id;
    
    //     $coupons = Coupon::where('status', 'active')->get();
    //     // dd($coupons);
    //     if (!$coupons->isEmpty()) {
    //         $userRedeemedCoupons = DB::table('coupon_users')
    //                                 ->where('user_id', $user_id)
    //                                 ->pluck('coupon_id')
    //                                 ->toArray();
    
    //         $couponsData = [];
            
    //         foreach ($coupons as $coupon) {
    //             $isUsed = in_array($coupon->id, $userRedeemedCoupons);
    //             $couponData = $coupon->toArray();
    //             $couponData['is_used'] = $isUsed;
    //             $couponData['end_date_time'] = date('d/m/Y',strtotime($coupon->end_date_time));
    //             $couponsData[] = $couponData;
    //         }
    
    //        if ($isUsed == false) {
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'success',
    //                 'data' => $couponsData,
    //             ]);
    //        }
    //     } else {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data not found',
    //             'data' => [],
    //         ]);
    //     }
    // }

    // public function couponStatusCheck(Request $request)
    // {
    //     $request->validate([
    //         'coupon_id' => 'required|string',
    //     ]);
    //     $user_id = Auth::user()->id;
    //     $coupon = Coupon::where('id', $request->input('coupon_id'))
    //                 ->first();
        
                    
    //     if($coupon){
    //         $isUsed = DB::table('coupon_users')
    //             ->where('user_id', $user_id)
    //             ->where('coupon_id', $coupon->id)
    //             ->exists();

    //         return response()->json([
    //             'status' => true,
    //             'data' => [
    //                 'is_used' => $isUsed
    //             ],
    //         ]);
    //     }else{
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Coupon is not exist',
    //         ]);
    //     }
    // }

    public function redeemable_coupon(Request $request)
    {
        $user_id = Auth::user()->id;

        $userRedeemedCoupons = CouponUser::where('user_id', $user_id)
                                            ->pluck('coupon_id')
                                            ->toArray();
    
        $coupons = Coupon::where('status', 'active')
                            ->whereIn('id', $userRedeemedCoupons)
                            ->get();

        if (!$coupons->isEmpty()) 
        {
            foreach ($coupons as $item) 
            {
                $item->end_date = date('d/m/Y', strtotime($item->end_date_time));
            }
           
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $coupons,
            ]);
        } 
        else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => [],
            ]);
        }
    }
}
