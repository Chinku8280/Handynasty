<?php

namespace App\Http\Controllers\Api;

use App\BusinessService;
use App\Coupon;
use App\CouponRedeem;
use App\CouponUsage;
use App\CouponUser;
use App\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoyaltyPoint;
use App\Outlet;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function my_coupons(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        // $coupon_id_arr = CouponUser::where('user_id', $user_id)->pluck('coupon_id')->toArray();

        // $customer_coupons = Coupon::where('status', 'active')
        //                     ->whereDate('start_date_time', '<=', date('Y-m-d'))
        //                     ->whereDate('end_date_time', '>=', date('Y-m-d'))
        //                     ->whereIn('id', $coupon_id_arr)
        //                     ->get();

        // $all_coupons = Coupon::where('status', 'active')
        //                     ->whereDate('start_date_time', '<=', date('Y-m-d'))
        //                     ->whereDate('end_date_time', '>=', date('Y-m-d'))
        //                     ->where('is_customer_specific', 0)
        //                     ->get();

        // $coupons = $all_coupons->merge($customer_coupons);

        $redeem_coupon_id_arr = CouponRedeem::where('user_id', $user_id)->pluck('coupon_id')->toArray();

        $redeem_coupons = Coupon::where('status', 'active')
                            ->whereDate('start_date_time', '<=', date('Y-m-d'))
                            ->whereDate('end_date_time', '>=', date('Y-m-d'))
                            ->whereIn('id', $redeem_coupon_id_arr)
                            ->get();

        $coupons = $redeem_coupons;

        $filtered_coupons = collect();

        if (!$coupons->isEmpty())
        {
            foreach ($coupons as $item) 
            {
                $item->end_date = date('d/m/Y', strtotime($item->end_date_time));

                // check coupon uses limit active or not

                if($item->uses_limit == null || $item->uses_limit == "")
                {
                    $item->used_status = 'active';
                }
                else if($item->uses_limit == 0)
                {
                    $item->used_status = 'deactive';
                }
                else
                {
                    if(($item->used_time != $item->uses_limit))
                    {
                        $item->used_status = 'active';
                    }
                    else
                    {
                        $item->used_status = 'deactive';
                    }
                }

                // check coupon used or not

                if(CouponUsage::where('user_id', $user_id)->where('coupon_id', $item->id)->exists())
                {
                    $item->isUsed = true;
                }
                else
                {
                    $item->isUsed = false;
                }   
                
                // check age

                if($item->max_age >= $user_age && $item->min_age <= $user_age)
                {
                    $item->isAgeEligible = true;
                }
                else
                {
                    $item->isAgeEligible = false;
                }

                // check gender

                if(DB::table('coupon_gender')->where('coupon_id', $item->id)->where('gender', $user_gender)->exists())
                {
                    $item->isGenderEligible = true;
                }
                else
                {
                    $item->isGenderEligible = false;
                }

                // other flag (only for mobile app)
                $item->redeem_status = true;

                // Apply filter condition
                // if ($item->used_status == 'active' && $item->isUsed === false && $item->isAgeEligible === true && $item->isGenderEligible === true) 
                // {
                //     $filtered_coupons->push($item);
                // }

                // Apply filter condition
                if ($item->used_status == 'active' && $item->isUsed === false) 
                {
                    $filtered_coupons->push($item);
                }
            }
           
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $filtered_coupons,
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

    public function coupon_details(Request $request)
    {
        $user_id = Auth::user()->id;

        $coupon_id = $request->coupon_id;

        $coupon = Coupon::find($coupon_id);

        if ($coupon)
        {
            $coupon->description_filter = strip_tags($coupon->description);

            $coupon->start_date = date('d F Y', strtotime($coupon->start_date_time));
            $coupon->end_date = date('d F Y', strtotime($coupon->end_date_time));

            // services

            $business_service_id = DB::table('coupon_services')->where('coupon_id', $coupon_id)->pluck('business_service_id')->toArray();
            $services = DB::table('business_services')->whereIn('id', $business_service_id)->select('id', 'name')->get();
            $service_name_arr = DB::table('business_services')->whereIn('id', $business_service_id)->pluck('name')->toArray();
            
            $coupon->services_name = implode(', ', $service_name_arr);
            $coupon->services = $services;

            // outlet

            $outlet_id = DB::table('coupon_outlets')->where('coupon_id', $coupon_id)->pluck('outlet_id')->toArray();
            $outlet = Outlet::whereIn('id', $outlet_id)->select('id', 'outlet_name')->get();
            $outlet_name_arr = Outlet::whereIn('id', $outlet_id)->pluck('outlet_name')->toArray();
            
            $coupon->outlet_name = implode(', ', $outlet_name_arr);
            $coupon->outlet = $outlet;


            if(CouponRedeem::where('user_id', $user_id)->where('coupon_id', $coupon_id)->exists())
            {
                $coupon->isRedeemed = true;
            }
            else
            {
                $coupon->isRedeemed = false;
            }

            if(CouponUsage::where('user_id', $user_id)->where('coupon_id', $coupon_id)->exists())
            {
                $coupon->isUsed = true;
            }
            else
            {
                $coupon->isUsed = false;
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $coupon
            ]);
        }
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Coupon not found',
                'data' => null
            ]);
        }
    }

    public function search_coupons(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $search = $request->search;

        $coupon_id_arr = CouponRedeem::where('user_id', $user_id)->pluck('coupon_id')->toArray();

        $coupons = Coupon::where('status', 'active')
                        ->where('coupon_code', 'like', '%' . $search . '%')
                        ->whereDate('start_date_time', '<=', date('Y-m-d'))
                        ->whereDate('end_date_time', '>=', date('Y-m-d'))
                        ->whereNotIn('id', $coupon_id_arr)
                        ->get();

        $filtered_coupons = collect();

        if (!$coupons->isEmpty())
        {
            foreach ($coupons as $item) 
            {
                // coupon is assigned or not
                if($item->is_customer_specific == 1)
                {
                    if(CouponUser::where('user_id', $user_id)->where('coupon_id', $item->id)->exists())                  
                    {
                        $item->customer_can_redeem = true;
                    }
                    else
                    {
                        $item->customer_can_redeem = false;
                    }
                }
                else
                {
                    $item->customer_can_redeem = true;
                }

                // end date
                $item->end_date = date('d/m/Y', strtotime($item->end_date_time));

                // check coupon uses limit active or not

                if($item->uses_limit == null || $item->uses_limit == "")
                {
                    $item->used_status = 'active';
                }
                else if($item->uses_limit == 0)
                {
                    $item->used_status = 'deactive';
                }
                else
                {
                    if(($item->used_time != $item->uses_limit))
                    {
                        $item->used_status = 'active';
                    }
                    else
                    {
                        $item->used_status = 'deactive';
                    }
                }

                // check coupon used or not

                if(CouponUsage::where('user_id', $user_id)->where('coupon_id', $item->id)->exists())
                {
                    $item->isUsed = true;
                }
                else
                {
                    $item->isUsed = false;
                }   
                
                // check age

                if($item->max_age >= $user_age && $item->min_age <= $user_age)
                {
                    $item->isAgeEligible = true;
                }
                else
                {
                    $item->isAgeEligible = false;
                }

                // check gender

                if(DB::table('coupon_gender')->where('coupon_id', $item->id)->where('gender', $user_gender)->exists())
                {
                    $item->isGenderEligible = true;
                }
                else
                {
                    $item->isGenderEligible = false;
                }

                // other flag (only for mobile app)
                $item->redeem_status = false;

                // Apply filter condition
                // if ($item->used_status == 'active' && $item->customer_can_redeem === true && $item->isUsed === false && $item->isAgeEligible === true && $item->isGenderEligible === true) 
                // {
                //     $filtered_coupons->push($item);
                // }

                // Apply filter condition
                if ($item->used_status == 'active' && $item->customer_can_redeem === true && $item->isUsed === false) 
                {
                    $filtered_coupons->push($item);
                }
            }
            
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $filtered_coupons,
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

    public function redeem_coupons(Request $request) 
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $coupon_id = $request->coupon_id;
        $coupon = Coupon::find($coupon_id);

        if($coupon)
        {       
            // check coupon uses limit active or not end

            if($coupon->is_customer_specific == 0)
            {
                $customer_can_redeem = "yes";
            }
            else
            {
                if(CouponUser::where('user_id', $user_id)->where('coupon_id', $coupon_id)->exists())
                {
                    $customer_can_redeem = "yes";
                }
                else
                {
                    $customer_can_redeem = "no";
                }
            }

            if(strtotime(date('Y-m-d')) >= strtotime($coupon->start_date_time) && strtotime(date('Y-m-d')) <= strtotime($coupon->end_date_time)) 
            {
                // if($coupon->max_age >= $user_age && $coupon->min_age <= $user_age)
                // {
                //     if(DB::table('coupon_gender')->where('coupon_id', $coupon_id)->where('gender', $user_gender)->exists())
                //     {

                        if($customer_can_redeem == "yes")
                        {
                            if(count(CouponRedeem::where('user_id', $user_id)->where('coupon_id', $coupon_id)->get()) == 0)
                            {
                                $coupon_redeem = new CouponRedeem();

                                $coupon_redeem->coupon_id = $coupon_id;
                                $coupon_redeem->user_id = $user_id;

                                $result = $coupon_redeem->save();

                                if($result)
                                {                                                                    
                                    return response()->json([
                                        'status' => true,
                                        'message' => "Coupon is Redeemed",
                                    ]);
                                }
                                else
                                {
                                    return response()->json([
                                        'status' => false,
                                        'message' => "Coupon is not Redeemed",
                                    ]);
                                }
                            }
                            else
                            {
                                return response()->json([
                                    'status' => false,
                                    'message' => "You have already redeemed this coupon",
                                ]);
                            }
                        }
                        else
                        {
                            return response()->json([
                                'status' => false,
                                'message' => 'You can not redeem this coupon'
                            ]);
                        }

                //     }
                //     else
                //     {
                //         return response()->json([
                //             'status' => false,
                //             'message' => 'You are not eligible to use this coupon'
                //         ]);
                //     }  
                // }
                // else
                // {
                //     return response()->json([
                //         'status' => false,
                //         'message' => 'Your age is not eligible to use this coupon'
                //     ]);
                // }   
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => "Coupon expired or not started"
                ]);
            }  
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => "Coupon not found",
            ]);
        }
    }
}
